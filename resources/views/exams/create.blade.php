@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Create New Exam</h1>
        <p class="text-slate-600 mt-2">Set up the exam first, then add MCQ questions on the next screen.</p>
    </div>

    <div class="bg-white shadow-xl rounded-2xl border border-slate-200 overflow-hidden">
        <form action="{{ route('exams.store') }}" method="POST" class="p-8 space-y-6">
            @csrf

            <div>
                <label for="title" class="block text-sm font-semibold text-slate-700 mb-2">Exam Title</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required class="w-full rounded-lg border-slate-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="e.g. Midterm Physics 101">
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-slate-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="3" class="w-full rounded-lg border-slate-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="Brief overview of the exam...">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="duration_minutes" class="block text-sm font-semibold text-slate-700 mb-2">Duration (minutes)</label>
                    <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', 60) }}" min="1" max="480" required class="w-full rounded-lg border-slate-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                </div>
                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">Access Code (optional)</label>
                    <input type="password" name="password" id="password" class="w-full rounded-lg border-slate-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="start_time" class="block text-sm font-semibold text-slate-700 mb-2">Start Time</label>
                    <input type="datetime-local" name="start_time" id="start_time" value="{{ old('start_time') }}" class="w-full rounded-lg border-slate-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                </div>
                <div>
                    <label for="end_time" class="block text-sm font-semibold text-slate-700 mb-2">End Time</label>
                    <input type="datetime-local" name="end_time" id="end_time" value="{{ old('end_time') }}" class="w-full rounded-lg border-slate-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end space-x-4">
                <a href="{{ route('exams.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-800 transition">Cancel</a>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-bold rounded-xl shadow-lg shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 transition duration-200">
                    Create Exam & Add Questions
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
