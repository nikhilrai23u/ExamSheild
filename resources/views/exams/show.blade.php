@extends('layouts.app')

@section('content')
<div class="mb-8 flex justify-between items-start">
    <div>
        <nav class="flex text-sm text-slate-500 mb-2">
            <a href="{{ route('exams.index') }}" class="hover:text-slate-700 transition">Exams</a>
            <span class="mx-2">/</span>
            <span class="text-slate-900 font-medium">{{ $exam->title }}</span>
        </nav>
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $exam->title }}</h1>
        <p class="text-slate-600 mt-2">{{ $exam->description }}</p>
    </div>
    <div class="flex space-x-3">
        <a href="{{ route('exams.edit', $exam) }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 rounded-lg font-semibold text-xs text-slate-700 uppercase tracking-widest shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
            Edit Exam
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-bold text-slate-900">Questions ({{ $exam->questions->count() }})</h2>
        </div>

        @forelse($exam->questions as $index => $question)
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <div class="flex justify-between items-start mb-4">
                    <span class="text-sm font-bold text-blue-600 uppercase tracking-wider">Question {{ $index + 1 }}</span>
                    <span class="bg-slate-100 text-slate-600 px-2 py-1 rounded text-xs font-bold">{{ strtoupper($question->type) }}</span>
                </div>
                <p class="text-slate-800 font-medium text-lg mb-4">{{ $question->question_text }}</p>
                
                @if($question->options)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                        @foreach($question->options as $optionIndex => $option)
                            <div class="p-3 rounded-lg border {{ $question->correct_answer == $optionIndex ? 'bg-green-50 border-green-200 text-green-700 font-semibold' : 'bg-slate-50 border-slate-200 text-slate-600' }}">
                                {{ $option }}
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-3 rounded-lg border bg-green-50 border-green-200 text-green-700 font-semibold mb-4">
                        Correct: {{ $question->correct_answer }}
                    </div>
                @endif
                
                <div class="flex justify-end space-x-3 mt-4 pt-4 border-t border-slate-100">
                    <form action="{{ route('questions.destroy', $question) }}" method="POST" onsubmit="return confirm('Delete this question?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs font-bold text-red-500 hover:text-red-700">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-slate-50 rounded-xl border-2 border-dashed border-slate-300">
                <p class="text-slate-500">No questions added yet.</p>
            </div>
        @endforelse
    </div>

    <div class="lg:col-span-1">
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-lg sticky top-8">
            <h3 class="text-lg font-bold text-slate-900 mb-2">Add MCQ Question</h3>
            <p class="text-sm text-slate-500 mb-6">Enter four options and mark the correct answer.</p>
            <form action="{{ route('exams.questions.store', $exam) }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="type" value="mcq">

                <div>
                    <label for="question_text" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Question Text</label>
                    <textarea name="question_text" id="question_text" rows="2" required class="w-full rounded-lg border-slate-300 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Type the question here...">{{ old('question_text') }}</textarea>
                </div>
                
                <fieldset class="space-y-3">
                    <legend class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Answer Options</legend>

                    @for($optionIndex = 0; $optionIndex < 4; $optionIndex++)
                        <label class="flex items-center gap-3 rounded-lg border border-slate-200 p-3">
                            <input type="radio" name="correct_answer" value="{{ $optionIndex }}" required @checked((string) old('correct_answer', '0') === (string) $optionIndex) class="h-4 w-4 border-slate-300 text-blue-600 focus:ring-blue-500">
                            <input type="text" name="options[]" value="{{ old("options.{$optionIndex}") }}" required class="min-w-0 flex-1 rounded-lg border-slate-300 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Option {{ $optionIndex + 1 }}">
                        </label>
                    @endfor
                </fieldset>

                <div>
                    <label for="points" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Points</label>
                    <input type="number" name="points" id="points" value="{{ old('points', 1) }}" min="1" required class="w-full rounded-lg border-slate-300 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>

                <button type="submit" class="w-full mt-4 py-2 bg-blue-600 text-white font-bold rounded-lg shadow-md shadow-blue-100 hover:bg-blue-700 transition">
                    Add Question
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
