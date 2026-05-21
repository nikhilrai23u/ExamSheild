@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-8">
        <nav class="flex text-sm text-slate-500 mb-2">
            <a href="{{ route('student.dashboard') }}" class="hover:text-slate-700 transition">Dashboard</a>
            <span class="mx-2">/</span>
            <span class="text-slate-900 font-medium">{{ $session->exam->title }} Result</span>
        </nav>
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Result Panel</h1>
        <p class="text-slate-600 mt-2">Your score and proctoring summary for <strong>{{ $session->exam->title }}</strong>.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Score</p>
            <p class="text-3xl font-black text-blue-600 mt-2">{{ $session->score ?? 0 }} / {{ $totalPoints }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Status</p>
            <p class="text-2xl font-black {{ $session->status === 'cancelled' ? 'text-red-600' : 'text-slate-900' }} mt-2">{{ ucfirst(str_replace('_', ' ', $session->status)) }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Proctoring Incidents</p>
            <p class="text-3xl font-black text-red-600 mt-2">{{ $session->logs->where('event_type', '!=', 'heartbeat')->count() }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Attempt Details</h2>
        </div>
        <dl class="divide-y divide-slate-100">
            <div class="px-6 py-4 flex justify-between">
                <dt class="text-sm font-medium text-slate-500">Started</dt>
                <dd class="text-sm font-bold text-slate-900">{{ $session->start_time->format('M d, Y h:i A') }}</dd>
            </div>
            <div class="px-6 py-4 flex justify-between">
                <dt class="text-sm font-medium text-slate-500">Submitted</dt>
                <dd class="text-sm font-bold text-slate-900">{{ $session->end_time?->format('M d, Y h:i A') ?? 'Not submitted' }}</dd>
            </div>
            <div class="px-6 py-4 flex justify-between">
                <dt class="text-sm font-medium text-slate-500">Questions</dt>
                <dd class="text-sm font-bold text-slate-900">{{ $session->exam->questions->count() }}</dd>
            </div>
        </dl>
    </div>

    @include('partials.answer-review', ['session' => $session])
</div>
@endsection
