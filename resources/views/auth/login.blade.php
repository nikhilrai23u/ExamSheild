@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto">
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Log in</h1>
        <p class="text-slate-600 mt-2">Access your proctored exam workspace.</p>
    </div>

    <form action="{{ route('login') }}" method="POST" class="bg-white p-8 rounded-xl border border-slate-200 shadow-sm space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus class="w-full rounded-lg border-slate-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
        </div>

        <div>
            <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
            <input type="password" name="password" id="password" required class="w-full rounded-lg border-slate-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
        </div>

        <label class="flex items-center text-sm text-slate-600">
            <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
            <span class="ml-2">Remember me</span>
        </label>

        <button type="submit" class="w-full py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition">
            Log in
        </button>

        <p class="text-center text-sm text-slate-500">
            New here?
            <a href="{{ route('register') }}" class="font-bold text-blue-600 hover:text-blue-800">Create an account</a>
        </p>
    </form>
</div>
@endsection
