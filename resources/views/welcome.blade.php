<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>ExamShield - Secure Proctored Exams</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                font-family: 'Inter', sans-serif;
            }
            h1, h2, h3 {
                font-family: 'Outfit', sans-serif;
            }
            .glass {
                background: rgba(255, 255, 255, 0.7);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
            .dark .glass {
                background: rgba(15, 23, 42, 0.7);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
        </style>
    </head>
    <body class="antialiased bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen">
        <!-- Navigation Header -->
        <nav class="fixed top-0 w-full z-50 glass">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white font-black shadow-lg shadow-blue-500/20">
                            E
                        </div>
                        <span class="text-xl font-bold tracking-tight">Exam<span class="text-blue-600">Shield</span></span>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ route('dashboard') }}" class="text-sm font-semibold hover:text-blue-600 transition">Dashboard</a>
                                <form method="POST" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-sm font-semibold text-slate-500 hover:text-red-600 transition cursor-pointer">Log Out</button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="text-sm font-semibold hover:text-blue-600 transition">Log in</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 transition">Get Started</a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full -z-10 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-from)_0%,_transparent_70%)] from-blue-100/50 dark:from-blue-900/20"></div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
                <div class="text-center max-w-3xl mx-auto">
                    <div class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs font-bold uppercase tracking-widest mb-6">
                        <span class="flex h-2 w-2 rounded-full bg-blue-600 mr-2 animate-pulse"></span>
                        Trusted by 500+ Institutions
                    </div>
                    
                    <h1 class="text-5xl lg:text-7xl font-black mb-6 tracking-tight leading-tight">
                        Secure Exams, <br/>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Guaranteed Integrity.</span>
                    </h1>
                    
                    <p class="text-xl text-slate-600 dark:text-slate-400 mb-10 leading-relaxed">
                        ExamShield provides a robust, AI-powered proctoring environment. Prevent cheating, automate grading, and gain deeper insights into student performance.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        @auth
                            @if(auth()->user()->role === 'teacher')
                                <a href="{{ route('exams.index') }}" class="px-8 py-4 bg-blue-600 text-white font-extrabold rounded-2xl shadow-2xl shadow-blue-500/30 hover:bg-blue-700 transition transform hover:-translate-y-1 text-lg">
                                    Manage Exams
                                </a>
                                <a href="{{ route('exams.create') }}" class="px-8 py-4 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-bold rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-blue-600 transition text-lg">
                                    Create New Exam
                                </a>
                            @else
                                <a href="{{ route('dashboard') }}" class="px-8 py-4 bg-blue-600 text-white font-extrabold rounded-2xl shadow-2xl shadow-blue-500/30 hover:bg-blue-700 transition transform hover:-translate-y-1 text-lg">
                                    My Exams Dashboard
                                </a>
                            @endif
                        @else
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-8 py-4 bg-blue-600 text-white font-extrabold rounded-2xl shadow-2xl shadow-blue-500/30 hover:bg-blue-700 transition transform hover:-translate-y-1 text-lg">
                                    Start Free Trial
                                </a>
                            @endif
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}" class="px-8 py-4 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-bold rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-blue-600 transition text-lg">
                                    Existing User login
                                </a>
                            @endif
                            @if (!Route::has('login') && !Route::has('register'))
                                <p class="text-slate-500 italic">Authentication system is being configured. Please contact the administrator.</p>
                            @endif
                        @endauth
                    </div>
                </div>

                <!-- Feature Grid -->
                <div class="mt-32 grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="glass p-8 rounded-3xl group hover:border-blue-500/50 transition">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-blue-600 mb-6 group-hover:scale-110 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Anti-Cheat Engine</h3>
                        <p class="text-slate-500">Real-time browser lock and anomaly detection to ensure a level playing field.</p>
                    </div>
                    
                    <div class="glass p-8 rounded-3xl group hover:border-blue-500/50 transition border-blue-500/30">
                        <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center text-indigo-600 mb-6 group-hover:scale-110 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Instant Analytics</h3>
                        <p class="text-slate-500">Detailed performance reports and automated grading as soon as the exam ends.</p>
                    </div>

                    <div class="glass p-8 rounded-3xl group hover:border-blue-500/50 transition">
                        <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center text-emerald-600 mb-6 group-hover:scale-110 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Flexible Scheduling</h3>
                        <p class="text-slate-500">Set precise durations, windows, and attempts for your exams with ease.</p>
                    </div>
                </div>
            </div>
        </div>

        <footer class="py-10 border-t border-slate-200 dark:border-slate-800">
            <div class="max-w-7xl mx-auto px-4 text-center text-slate-500 text-sm">
                &copy; {{ date('Y') }} ExamShield. Built for academic excellence.
            </div>
        </footer>
    </body>
</html>
