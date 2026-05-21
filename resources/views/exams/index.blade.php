@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Your Exams</h1>
    <a href="{{ route('exams.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
        Create New Exam
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($exams as $exam)
        @php
            $examStatus = 'Open';
            $statusClasses = 'bg-green-100 text-green-700';

            if ($exam->questions_count === 0) {
                $examStatus = 'Draft';
                $statusClasses = 'bg-slate-100 text-slate-600';
            } elseif ($exam->start_time && now()->lt($exam->start_time)) {
                $examStatus = 'Scheduled';
                $statusClasses = 'bg-blue-100 text-blue-700';
            } elseif ($exam->end_time && now()->gt($exam->end_time)) {
                $examStatus = 'Closed';
                $statusClasses = 'bg-red-100 text-red-700';
            }
        @endphp
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-slate-200 hover:shadow-md transition duration-200">
            <div class="p-6 text-slate-900">
                <div class="flex items-start justify-between gap-4 mb-2">
                    <h3 class="text-xl font-bold">{{ $exam->title }}</h3>
                    <span class="shrink-0 rounded-full px-2 py-1 text-xs font-bold {{ $statusClasses }}">{{ $examStatus }}</span>
                </div>
                <p class="text-slate-600 text-sm mb-4 line-clamp-2">{{ $exam->description }}</p>
                <div class="flex items-center text-sm text-slate-500 space-x-4 mb-4">
                    <span class="flex items-center">
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ $exam->duration_minutes }} mins
                    </span>
                    <span class="flex items-center">
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ $exam->questions_count }} Questions
                    </span>
                    <span class="flex items-center">
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m0-4a4 4 0 100-8 4 4 0 000 8zm8 0a4 4 0 100-8 4 4 0 000 8z"></path></svg>
                        {{ $exam->sessions_count }} Attempts
                    </span>
                </div>
                @if($exam->start_time || $exam->end_time)
                    <div class="mb-4 rounded-lg bg-slate-50 p-3 text-xs text-slate-600">
                        @if($exam->start_time)
                            <div><strong>Starts:</strong> {{ $exam->start_time->format('M d, Y h:i A') }}</div>
                        @endif
                        @if($exam->end_time)
                            <div><strong>Ends:</strong> {{ $exam->end_time->format('M d, Y h:i A') }}</div>
                        @endif
                    </div>
                @endif
                <div class="mt-4 flex space-x-3">
                    <a href="{{ route('exams.show', $exam) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 transition">Questions</a>
                    <a href="{{ route('exams.results', $exam) }}" class="text-sm font-semibold text-green-600 hover:text-green-800 transition">Results</a>
                    <a href="{{ route('exams.edit', $exam) }}" class="text-sm font-semibold text-slate-600 hover:text-slate-800 transition">Edit</a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full py-12 text-center bg-white rounded-xl border-2 border-dashed border-slate-300">
            <p class="text-slate-500">No exams found. Start by creating one!</p>
        </div>
    @endforelse
</div>
@endsection
