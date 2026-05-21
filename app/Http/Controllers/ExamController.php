<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $exams = Exam::withCount(['questions', 'sessions'])->latest()->get();

        return view('exams.index', compact('exams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('exams.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:480'],
            'start_time' => ['nullable', 'date'],
            'end_time' => ['nullable', 'date', 'after_or_equal:start_time'],
            'password' => ['nullable', 'string', 'max:255'],
        ]);

        if (filled($validated['password'] ?? null)) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $exam = Exam::create($validated);

        return redirect()->route('exams.show', $exam)->with('status', 'Exam created successfully. Add questions below.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Exam $exam): View
    {
        $exam->load('questions');

        return view('exams.show', compact('exam'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exam $exam): View
    {
        return view('exams.edit', compact('exam'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Exam $exam): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:480'],
            'start_time' => ['nullable', 'date'],
            'end_time' => ['nullable', 'date', 'after_or_equal:start_time'],
            'password' => ['nullable', 'string', 'max:255'],
            'clear_password' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('clear_password')) {
            $validated['password'] = null;
        } elseif (filled($validated['password'] ?? null)) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        unset($validated['clear_password']);

        $exam->update($validated);

        return redirect()->route('exams.show', $exam)->with('status', 'Exam updated successfully!');
    }

    public function results(Exam $exam): View
    {
        $securityViolationEvents = [
            'tab_switch',
            'window_blur',
            'fullscreen_exit',
            'copy_attempt',
            'paste_attempt',
            'context_menu',
            'print_attempt',
            'keyboard_shortcut',
            'developer_tools_suspected',
        ];

        $sessions = $exam->sessions()
            ->with('user')
            ->withCount([
                'logs',
                'logs as security_violations_count' => fn ($query) => $query->whereIn('event_type', $securityViolationEvents),
            ])
            ->latest()
            ->get();

        $totalPoints = $exam->questions()->sum('points');
        $completedSessions = $sessions->where('status', 'completed');
        $analytics = [
            'attempts' => $sessions->count(),
            'completed' => $completedSessions->count(),
            'cancelled' => $sessions->where('status', 'cancelled')->count(),
            'average_score' => round((float) ($completedSessions->avg('score') ?? 0), 1),
            'highest_score' => (int) ($completedSessions->max('score') ?? 0),
            'average_violations' => round((float) ($sessions->avg('security_violations_count') ?? 0), 1),
        ];

        return view('exams.results', compact('analytics', 'exam', 'sessions', 'totalPoints'));
    }

    public function sessionLogs(ExamSession $session): View
    {
        $session->load(['logs', 'user', 'exam.questions']);

        return view('exams.session_logs', compact('session'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam): RedirectResponse
    {
        $exam->delete();

        return redirect()->route('exams.index')->with('status', 'Exam deleted successfully!');
    }
}
