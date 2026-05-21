@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Edit Exam</h1>
        <p class="text-slate-600 mt-2">Update scheduling, duration, and access settings.</p>
    </div>

    <form action="{{ route('exams.update', $exam) }}" method="POST" class="bg-white shadow-xl rounded-2xl border border-slate-200 p-8 space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label for="title" class="block text-sm font-semibold text-slate-700 mb-2">Exam Title</label>
            <input type="text" name="title" id="title" value="{{ old('title', $exam->title) }}" required class="w-full rounded-lg border-slate-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
        </div>

        <div>
            <label for="description" class="block text-sm font-semibold text-slate-700 mb-2">Description</label>
            <textarea name="description" id="description" rows="3" class="w-full rounded-lg border-slate-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm">{{ old('description', $exam->description) }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="duration_minutes" class="block text-sm font-semibold text-slate-700 mb-2">Duration (minutes)</label>
                <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', $exam->duration_minutes) }}" required min="1" max="480" class="w-full rounded-lg border-slate-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
            </div>
            <div>
                <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">New Access Code</label>
                <input type="password" name="password" id="password" class="w-full rounded-lg border-slate-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="{{ $exam->hasAccessCode() ? 'Leave blank to keep current code' : 'Optional' }}">
            </div>
        </div>

        @if($exam->hasAccessCode())
            <label class="flex items-center text-sm text-slate-600">
                <input type="checkbox" name="clear_password" value="1" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <span class="ml-2">Remove current access code</span>
            </label>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="start_time" class="block text-sm font-semibold text-slate-700 mb-2">Start Time</label>
                <input type="datetime-local" name="start_time" id="start_time" value="{{ old('start_time', $exam->start_time?->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border-slate-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
            </div>
            <div>
                <label for="end_time" class="block text-sm font-semibold text-slate-700 mb-2">End Time</label>
                <input type="datetime-local" name="end_time" id="end_time" value="{{ old('end_time', $exam->end_time?->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border-slate-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
            </div>
        </div>

        <div class="pt-4 flex items-center justify-between">
            <button type="submit" form="delete-exam-form" class="text-sm font-bold text-red-600 hover:text-red-800" onclick="return confirm('Delete this exam and all related attempts?')">Delete Exam</button>
            <div class="flex items-center space-x-4">
                <a href="{{ route('exams.show', $exam) }}" class="text-sm font-semibold text-slate-600 hover:text-slate-800 transition">Cancel</a>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-bold rounded-xl shadow-lg shadow-blue-200 hover:bg-blue-700 transition">
                    Save Changes
                </button>
            </div>
        </div>
    </form>

    <form id="delete-exam-form" action="{{ route('exams.destroy', $exam) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection
