@extends('layouts.app')

@section('content')
<div class="mb-8 flex justify-between items-end">
    <div>
        <nav class="flex text-sm text-slate-500 mb-2">
            <a href="{{ route('exams.index') }}" class="hover:text-slate-700 transition">Exams</a>
            <span class="mx-2">/</span>
            <span class="text-slate-900 font-medium">{{ $exam->title }} Results</span>
        </nav>
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Exam Results</h1>
        <p class="text-slate-600 mt-2">Student submissions and integrity monitoring for <strong>{{ $exam->title }}</strong>. Total points: <strong>{{ $totalPoints }}</strong>.</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Attempts</p>
        <p class="mt-2 text-2xl font-black text-slate-900">{{ $analytics['attempts'] }}</p>
    </div>
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Completed</p>
        <p class="mt-2 text-2xl font-black text-green-600">{{ $analytics['completed'] }}</p>
    </div>
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Cancelled</p>
        <p class="mt-2 text-2xl font-black text-red-600">{{ $analytics['cancelled'] }}</p>
    </div>
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Avg Score</p>
        <p class="mt-2 text-2xl font-black text-blue-600">{{ $analytics['average_score'] }}</p>
    </div>
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">High Score</p>
        <p class="mt-2 text-2xl font-black text-indigo-600">{{ $analytics['highest_score'] }}</p>
    </div>
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Avg Violations</p>
        <p class="mt-2 text-2xl font-black text-amber-600">{{ $analytics['average_violations'] }}</p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <table class="w-full text-left">
        <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Student</th>
                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Score</th>
                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Proctoring Logs</th>
                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @forelse($sessions as $session)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-slate-900">{{ $session->user->name }}</div>
                        <div class="text-xs text-slate-500">{{ $session->user->email }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-bold rounded-full {{ $session->status === 'completed' ? 'bg-green-100 text-green-700' : ($session->status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') }}">
                            {{ ucfirst($session->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm font-extrabold text-slate-900">{{ $session->score ?? '-' }} / {{ $totalPoints }}</td>
                    <td class="px-6 py-4">
                        @if($session->security_violations_count > 0)
                            <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">
                                {{ $session->security_violations_count }} Security Violations
                            </span>
                        @else
                            <span class="text-xs text-slate-400 font-medium">None</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <a href="{{ route('sessions.logs', $session) }}" class="text-blue-600 font-bold hover:text-blue-800 transition">Review Logs</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-500 italic font-medium">No students have taken this exam yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
