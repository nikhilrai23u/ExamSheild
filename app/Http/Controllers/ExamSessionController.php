<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ExamSessionController extends Controller
{
    public function index(): View
    {
        $exams = Exam::query()
            ->withCount('questions')
            ->where(function ($query): void {
                $query->whereNull('start_time')->orWhere('start_time', '<=', now());
            })
            ->where(function ($query): void {
                $query->whereNull('end_time')->orWhere('end_time', '>=', now());
            })
            ->latest()
            ->get();

        $mySessions = auth()->user()->examSessions()->with('exam')->latest()->get();
        $dashboardStats = [
            'available_exams' => $exams->count(),
            'attempts' => $mySessions->count(),
            'completed' => $mySessions->where('status', 'completed')->count(),
            'cancelled' => $mySessions->where('status', 'cancelled')->count(),
        ];

        return view('student.dashboard', compact('dashboardStats', 'exams', 'mySessions'));
    }

    public function start(Request $request, Exam $exam): RedirectResponse
    {
        if (! $exam->isOpenForAttempt()) {
            return back()->withErrors(['exam' => 'This exam is not currently open.']);
        }

        if (! $exam->questions()->exists()) {
            return back()->withErrors(['exam' => 'This exam is not ready yet.']);
        }

        if ($exam->hasAccessCode()) {
            $request->validate([
                'password' => ['required', 'string'],
            ]);

            if (! Hash::check($request->string('password')->toString(), $exam->password)) {
                throw ValidationException::withMessages([
                    'password' => 'The exam access code is incorrect.',
                ]);
            }
        }

        $existingSession = auth()->user()
            ->examSessions()
            ->where('exam_id', $exam->id)
            ->whereIn('status', ['active', 'completed'])
            ->latest()
            ->first();

        if ($existingSession?->status === 'active') {
            return redirect()->route('exams.take', [$exam, $existingSession]);
        }

        if ($existingSession?->status === 'completed') {
            return back()->withErrors(['exam' => 'You have already completed this exam.']);
        }

        $session = auth()->user()->examSessions()->create([
            'exam_id' => $exam->id,
            'start_time' => now(),
            'status' => 'active',
        ]);

        return redirect()->route('exams.take', [$exam, $session]);
    }

    public function take(Exam $exam, ExamSession $session): View|RedirectResponse
    {
        $this->authorizeSession($exam, $session);

        $session->load('exam');

        if ($session->isExpired()) {
            $session->update([
                'end_time' => now(),
                'status' => 'timed_out',
                'score' => 0,
            ]);

            return redirect()->route('student.dashboard')->with('status', 'Your exam session timed out.');
        }

        $exam->load('questions');

        return view('student.take', [
            'exam' => $exam,
            'remainingSeconds' => $session->remainingSeconds(),
            'session' => $session,
        ]);
    }

    public function submit(Request $request, Exam $exam, ExamSession $session): RedirectResponse
    {
        $this->authorizeSession($exam, $session);

        $exam->load('questions');
        $review = $this->reviewAnswers($exam, $request->input('answers', []));
        $score = $review['score'];
        $answers = $review['answers'];

        if ($session->fresh()->isExpired()) {
            $score = 0;
        }

        $timedOut = $session->fresh()->isExpired();

        $session->update([
            'end_time' => now(),
            'status' => $timedOut ? 'timed_out' : 'completed',
            'score' => $score,
            'answers' => $answers,
        ]);

        return redirect()->route('student.results', $session)->with('status', "Exam submitted! Your score: {$score}");
    }

    public function results(ExamSession $session): View
    {
        if ($session->user_id !== auth()->id()) {
            abort(403);
        }

        $session->load(['exam.questions', 'logs']);
        $totalPoints = $session->exam->questions->sum('points');

        return view('student.results', compact('session', 'totalPoints'));
    }

    private function authorizeSession(Exam $exam, ExamSession $session): void
    {
        if ($session->user_id !== auth()->id() || $session->exam_id !== $exam->id || $session->status !== 'active') {
            abort(403);
        }
    }

    /**
     * @param  array<int|string, mixed>  $answers
     * @return array{score: int, answers: array<int, array<string, mixed>>}
     */
    private function reviewAnswers(Exam $exam, array $answers): array
    {
        $score = 0;

        $review = $exam->questions->map(function (Question $question) use ($answers, &$score): array {
            $studentAnswer = $answers[$question->id] ?? null;
            $studentAnswerLabel = $this->answerLabel($question, $studentAnswer);
            $correctAnswerLabel = $this->answerLabel($question, $question->correct_answer);

            if ($studentAnswer === null) {
                $isCorrect = false;
            } elseif ($question->type === 'short_answer') {
                $isCorrect = strcasecmp(trim((string) $studentAnswer), trim($question->correct_answer)) === 0;
            } else {
                $isCorrect = (string) $studentAnswer === (string) $question->correct_answer;
            }

            if ($isCorrect) {
                $score += $question->points;
            }

            return [
                'question_id' => $question->id,
                'question_text' => $question->question_text,
                'type' => $question->type,
                'options' => $question->options ?? [],
                'student_answer' => $studentAnswer,
                'student_answer_label' => $studentAnswerLabel,
                'correct_answer' => $question->correct_answer,
                'correct_answer_label' => $correctAnswerLabel,
                'is_correct' => $isCorrect,
                'points' => $question->points,
                'earned_points' => $isCorrect ? $question->points : 0,
            ];
        })->values()->all();

        return [
            'score' => $score,
            'answers' => $review,
        ];
    }

    private function answerLabel(Question $question, mixed $answer): ?string
    {
        if ($answer === null) {
            return null;
        }

        if ($question->type === 'short_answer') {
            return (string) $answer;
        }

        return $question->options[(int) $answer] ?? (string) $answer;
    }
}
