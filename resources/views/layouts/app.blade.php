<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'ExamShield') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-900">
    <div class="min-h-screen flex flex-col">
        <nav class="bg-white border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <span class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">ExamShield</span>
                        </div>
                        <div class="hidden sm:-my-px sm:ml-6 sm:flex sm:space-x-8">
                            @auth
                                @if(auth()->user()->isTeacher())
                                    <a href="{{ route('exams.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-blue-500 text-sm font-medium text-slate-900">Exams</a>
                                @endif
                            @endauth
                        </div>
                    </div>
                    <div class="flex items-center">
                        @auth
                            <span class="text-sm font-medium text-slate-700 mr-4">{{ auth()->user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-sm font-medium text-slate-500 hover:text-slate-700">Logout</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700 mr-4">Login</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <main class="flex-grow py-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600 px-4 py-3 rounded-lg bg-green-50 border border-green-200">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 text-sm text-red-700 px-4 py-3 rounded-lg bg-red-50 border border-red-200">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>

        <footer class="bg-white border-t border-slate-200 py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-slate-500 text-sm">
                &copy; {{ date('Y') }} ExamShield. All rights reserved.
            </div>
        </footer>
    </div>
</body>
</html>
