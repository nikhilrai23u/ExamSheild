@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Student Dashboard</h1>
    <p class="text-slate-600 mt-2">View and take your available exams.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Available</p>
        <p class="mt-2 text-2xl font-black text-blue-600">{{ $dashboardStats['available_exams'] }}</p>
    </div>
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Attempts</p>
        <p class="mt-2 text-2xl font-black text-slate-900">{{ $dashboardStats['attempts'] }}</p>
    </div>
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Completed</p>
        <p class="mt-2 text-2xl font-black text-green-600">{{ $dashboardStats['completed'] }}</p>
    </div>
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Cancelled</p>
        <p class="mt-2 text-2xl font-black text-red-600">{{ $dashboardStats['cancelled'] }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <section>
        <h2 class="text-xl font-bold text-slate-900 mb-4">Available Exams</h2>
        <div class="space-y-4">
            @forelse($exams as $exam)
                @php
                    $attempt = $mySessions->firstWhere('exam_id', $exam->id);
                @endphp
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <div>
                        <h3 class="font-bold text-slate-900">{{ $exam->title }}</h3>
                        <p class="text-sm text-slate-500">{{ $exam->duration_minutes }} minutes / {{ $exam->questions_count }} questions</p>
                    </div>
                    <div class="mt-4">
                        @if(in_array($attempt?->status, ['completed', 'timed_out', 'cancelled'], true))
                            <a href="{{ route('student.results', $attempt) }}" class="inline-flex px-3 py-2 {{ $attempt?->status === 'cancelled' ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }} text-sm font-bold rounded-lg transition">
                                View Result
                            </a>
                        @else
                            <form action="{{ route('exams.start', $exam) }}" method="POST" class="space-y-3">
                                @csrf
                                @if($exam->hasAccessCode())
                                    <input type="password" name="password" required class="w-full rounded-lg border-slate-300 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Access code">
                                @endif
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 transition">
                                    {{ $attempt?->status === 'active' ? 'Resume Exam' : 'Start Exam' }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-slate-500 italic">No exams available at the moment.</p>
            @endforelse
        </div>
    </section>

    <section>
        <h2 class="text-xl font-bold text-slate-900 mb-4">My Recent Attempts</h2>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Exam</th>
                        <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Score</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($mySessions as $session)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ $session->exam->title }}</td>
                            <td class="px-6 py-4 text-sm text-slate-500">{{ $session->start_time->format('M d, Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-bold rounded-full {{ $session->status === 'completed' ? 'bg-green-100 text-green-700' : ($session->status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') }}">
                                    {{ ucfirst($session->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-slate-900">
                                @if(in_array($session->status, ['completed', 'timed_out', 'cancelled'], true))
                                    <a href="{{ route('student.results', $session) }}" class="text-blue-600 hover:text-blue-800">{{ $session->score ?? 0 }}</a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-500 italic">You haven't taken any exams yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
