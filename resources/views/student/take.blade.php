@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto" id="exam-container" x-data="proctoring">
    <div class="mb-8 flex justify-between items-center sticky top-0 bg-slate-50 py-4 z-10 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $exam->title }}</h1>
            <p class="text-sm text-slate-500">Duration: {{ $exam->duration_minutes }} minutes</p>
        </div>
        <div class="text-right">
            <div class="text-3xl font-mono font-bold text-blue-600" id="timer">--:--</div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Time Remaining</p>
        </div>
    </div>

    <form action="{{ route('exams.submit', [$exam, $session]) }}" method="POST" id="exam-form" class="opacity-40 pointer-events-none">
        @csrf
        <div class="space-y-8">
            @foreach($exam->questions as $index => $question)
                <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm">
                    <div class="flex items-center mb-6">
                        <span class="w-8 h-8 flex items-center justify-center bg-slate-100 rounded-full text-sm font-bold text-slate-600 mr-3">
                            {{ $index + 1 }}
                        </span>
                        <h3 class="text-xl font-semibold text-slate-800">{{ $question->question_text }}</h3>
                    </div>

                    @if($question->type === 'mcq' || $question->type === 'true_false')
                        <div class="space-y-3">
                            @foreach($question->options as $optionIndex => $option)
                                <label class="flex items-center p-4 rounded-xl border border-slate-100 hover:border-blue-200 hover:bg-blue-50 transition cursor-pointer group">
                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $optionIndex }}" required class="h-5 w-5 text-blue-600 border-slate-300 focus:ring-blue-500">
                                    <span class="ml-4 text-slate-700 group-hover:text-blue-900 transition font-medium">{{ $option }}</span>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <textarea name="answers[{{ $question->id }}]" rows="4" class="w-full rounded-xl border-slate-200 focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="Type your answer here..."></textarea>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-12 flex justify-center pb-12">
            <button type="submit" class="px-10 py-4 bg-blue-600 text-white text-lg font-bold rounded-2xl shadow-xl shadow-blue-200 hover:bg-blue-700 hover:-translate-y-1 transition duration-200">
                Submit Exam
            </button>
        </div>
    </form>
</div>

<div id="proctoring-warning" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/90 backdrop-blur-sm p-4">
    <div class="bg-white p-8 rounded-3xl max-w-lg w-full text-center shadow-2xl">
        <div class="w-20 h-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
        <h2 class="text-2xl font-bold text-slate-900 mb-4">Security Violation Detected</h2>
        <p class="text-slate-600 mb-8" id="warning-text"></p>
        <button onclick="document.getElementById('proctoring-warning').classList.add('hidden')" class="w-full py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition">
            I Understand, return to exam
        </button>
    </div>
</div>

<div id="fullscreen-gate" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950 text-white p-4">
    <div class="max-w-lg w-full text-center">
        <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-blue-600">
            <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 1v4m0 0h-4m4 0l-5-5"></path></svg>
        </div>
        <h2 class="text-3xl font-black tracking-tight">Fullscreen Required</h2>
        <p class="mt-3 text-slate-300">This exam can only be attempted in fullscreen mode. Tab switching, printing, copy/paste, and restricted shortcuts are monitored.</p>
        <button type="button" id="enter-fullscreen" class="mt-8 w-full rounded-xl bg-blue-600 px-6 py-4 text-lg font-bold text-white hover:bg-blue-700 transition">
            Enter Fullscreen & Begin
        </button>
    </div>
</div>

<script>
    let timeRemaining = {{ $remainingSeconds }};
    const sessionId = {{ $session->id }};
    const form = document.getElementById('exam-form');
    const fullscreenGate = document.getElementById('fullscreen-gate');
    let lastHeartbeatAt = 0;
    let isRedirecting = false;

    function setExamLocked(isLocked) {
        form.classList.toggle('opacity-40', isLocked);
        form.classList.toggle('pointer-events-none', isLocked);
        form.querySelectorAll('input, textarea, button').forEach((element) => {
            element.disabled = isLocked;
        });
    }

    function isFullscreen() {
        return Boolean(document.fullscreenElement);
    }

    async function enterFullscreen() {
        try {
            await document.documentElement.requestFullscreen();
            fullscreenGate.classList.add('hidden');
            setExamLocked(false);
            logProctoringEvent('fullscreen_entered', { remaining_seconds: timeRemaining });
        } catch (error) {
            showWarning('Fullscreen is required before you can continue the exam.');
        }
    }

    function startTimer() {
        const timerDisplay = document.getElementById('timer');
        const interval = setInterval(() => {
            const minutes = Math.floor(timeRemaining / 60);
            const seconds = timeRemaining % 60;
            timerDisplay.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
            
            if (timeRemaining <= 0) {
                clearInterval(interval);
                form.submit();
            }

            if (Date.now() - lastHeartbeatAt > 30000) {
                lastHeartbeatAt = Date.now();
                logProctoringEvent('heartbeat', { remaining_seconds: timeRemaining });
            }

            timeRemaining--;
        }, 1000);
    }

    async function logProctoringEvent(type, metadata = {}) {
        const response = await fetch('{{ route("proctoring.log") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                exam_session_id: sessionId,
                event_type: type,
                metadata: metadata
            })
        }).catch(() => {});

        if (!response) {
            return;
        }

        const payload = await response.json().catch(() => null);

        if (payload?.status === 'cancelled' && payload.redirect_url && !isRedirecting) {
            isRedirecting = true;
            alert(payload.message);
            window.location.href = payload.redirect_url;
        }
    }

    // Proctoring Logic
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            logProctoringEvent('tab_switch', { reason: 'Page hidden', remaining_seconds: timeRemaining });
            showWarning('You switched tabs/windows. The exam is cancelled after 3 security violations.');
        }
    });

    window.addEventListener('blur', () => {
        logProctoringEvent('window_blur', { reason: 'Window lost focus', remaining_seconds: timeRemaining });
        showWarning('The exam window lost focus. This event has been logged.');
    });

    window.addEventListener('resize', () => {
        if (window.innerHeight < screen.height * 0.8) {
            logProctoringEvent('fullscreen_exit', { reason: 'Substantial resize', remaining_seconds: timeRemaining });
        }
    });

    document.addEventListener('fullscreenchange', () => {
        if (isFullscreen()) {
            fullscreenGate.classList.add('hidden');
            setExamLocked(false);
            return;
        }

        setExamLocked(true);
        fullscreenGate.classList.remove('hidden');
        logProctoringEvent('fullscreen_exit', { reason: 'Fullscreen exited', remaining_seconds: timeRemaining });
    });

    document.addEventListener('copy', (e) => {
        e.preventDefault();
        logProctoringEvent('copy_attempt', { text: window.getSelection().toString() });
        showWarning('Copying text is not allowed during the exam.');
    });

    document.addEventListener('paste', (e) => {
        e.preventDefault();
        logProctoringEvent('paste_attempt', { reason: 'Paste blocked' });
        showWarning('Pasting text is not allowed during the exam.');
    });

    document.addEventListener('contextmenu', (e) => {
        e.preventDefault();
        logProctoringEvent('context_menu', { reason: 'Context menu blocked' });
    });

    window.addEventListener('beforeprint', () => {
        logProctoringEvent('print_attempt', { reason: 'Print dialog opened' });
        showWarning('Printing is not allowed during the exam.');
    });

    document.addEventListener('keydown', (e) => {
        const key = e.key.toLowerCase();
        const blockedWithCtrl = e.ctrlKey && ['c', 'v', 'x', 'p', 's', 'u'].includes(key);
        const blockedDevTools = e.key === 'F12' || (e.ctrlKey && e.shiftKey && ['i', 'j', 'c'].includes(key));

        if (blockedWithCtrl || blockedDevTools) {
            e.preventDefault();
            logProctoringEvent('keyboard_shortcut', { key: e.key, ctrl: e.ctrlKey, shift: e.shiftKey, alt: e.altKey });
            showWarning('That keyboard shortcut is not allowed during the exam.');
        }
    });

    setInterval(() => {
        const widthGap = window.outerWidth - window.innerWidth;
        const heightGap = window.outerHeight - window.innerHeight;

        if (widthGap > 160 || heightGap > 160) {
            logProctoringEvent('developer_tools_suspected', { width_gap: widthGap, height_gap: heightGap });
        }
    }, 10000);

    function showWarning(text) {
        document.getElementById('warning-text').textContent = text;
        document.getElementById('proctoring-warning').classList.remove('hidden');
    }

    // Start everything
    window.onload = () => {
        setExamLocked(true);
        startTimer();
        logProctoringEvent('session_started', { remaining_seconds: timeRemaining });
        document.getElementById('enter-fullscreen').addEventListener('click', enterFullscreen);
    };
</script>
@endsection
