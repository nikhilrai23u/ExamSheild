<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function store(Request $request, Exam $exam): RedirectResponse
    {
        $validated = $request->validate([
            'question_text' => ['required', 'string'],
            'type' => ['required', 'string', 'in:mcq'],
            'options' => ['required', 'array', 'size:4'],
            'options.*' => ['required', 'string', 'max:255'],
            'correct_answer' => ['required', 'integer', 'between:0,3'],
            'points' => ['required', 'integer', 'min:1'],
        ]);

        $validated['options'] = array_values($validated['options']);
        $validated['correct_answer'] = (string) $validated['correct_answer'];

        $exam->questions()->create($validated);

        return redirect()->route('exams.show', $exam)->with('status', 'Question added successfully!');
    }

    public function destroy(Question $question): RedirectResponse
    {
        $exam = $question->exam;
        $question->delete();

        return redirect()->route('exams.show', $exam)->with('status', 'Question deleted successfully!');
    }
}
