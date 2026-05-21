@extends('layouts.app')

@section('content')
<div class="mb-8">
    <nav class="flex text-sm text-slate-500 mb-2">
        <a href="{{ route('exams.index') }}" class="hover:text-slate-700 transition">Exams</a>
        <span class="mx-2">/</span>
        <a href="{{ route('exams.results', $session->exam) }}" class="hover:text-slate-700 transition">{{ $session->exam->title }} Results</a>
        <span class="mx-2">/</span>
        <span class="text-slate-900 font-medium">{{ $session->user->name }}'s Logs</span>
    </nav>
    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight text-red-600">Integrity Log</h1>
    <p class="text-slate-600 mt-2">Detailed proctoring events for <strong>{{ $session->user->name }}</strong>.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4">Attempt Overview</h3>
            <div class="space-y-4">
                <div>
                    <p class="text-xs font-bold text-slate-500">Student</p>
                    <p class="text-lg font-extrabold text-slate-900">{{ $session->user->name }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500">Score</p>
                    <p class="text-2xl font-black text-blue-600">{{ $session->score ?? 'N/A' }}</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-bold text-slate-500">Start Time</p>
                        <p class="text-sm font-medium text-slate-700">{{ $session->start_time->format('h:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-500">End Time</p>
                        <p class="text-sm font-medium text-slate-700">{{ $session->end_time ? $session->end_time->format('h:i A') : 'Ongoing' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-red-100 shadow-sm overflow-hidden">
            <div class="bg-red-50 px-6 py-4 border-b border-red-100 flex items-center">
                <svg class="h-5 w-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <h3 class="text-sm font-bold text-red-700 uppercase tracking-widest">Incidents Detected ({{ $session->logs->where('event_type', '!=', 'heartbeat')->count() }})</h3>
            </div>
            <div class="p-0">
                <ul class="divide-y divide-slate-100">
                    @forelse($session->logs as $log)
                        <li class="px-6 py-4 hover:bg-slate-50 transition flex items-start">
                            <div class="bg-red-100 text-red-600 p-2 rounded-lg mr-4">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="flex-grow">
                                <div class="flex justify-between">
                                    <h4 class="text-sm font-bold text-slate-900">{{ str_replace('_', ' ', strtoupper($log->event_type)) }}</h4>
                                    <span class="text-xs font-mono text-slate-400">{{ $log->event_timestamp->format('h:i:s A') }}</span>
                                </div>
                                <p class="text-sm text-slate-600 mt-1">
                                    {{ $log->metadata['reason'] ?? 'Violation detected by automated monitor.' }}
                                </p>
                            </div>
                        </li>
                    @empty
                        <li class="px-6 py-12 text-center text-slate-400 font-medium italic">
                            No suspicious activity detected during this session.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <div class="lg:col-span-3">
        @include('partials.answer-review', ['session' => $session])
    </div>
</div>
@endsection
