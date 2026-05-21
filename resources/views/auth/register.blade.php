@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto">
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Create account</h1>
        <p class="text-slate-600 mt-2">Join as a teacher or student.</p>
    </div>

    <form action="{{ route('register') }}" method="POST" class="bg-white p-8 rounded-xl border border-slate-200 shadow-sm space-y-5">
        @csrf

        <div>
            <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus class="w-full rounded-lg border-slate-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
        </div>

        <div>
            <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required class="w-full rounded-lg border-slate-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
        </div>

        <div>
            <label for="role" class="block text-sm font-semibold text-slate-700 mb-2">Account type</label>
            <select name="role" id="role" required class="w-full rounded-lg border-slate-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                <option value="student" @selected(old('role') === 'student')>Student</option>
                <option value="teacher" @selected(old('role') === 'teacher')>Teacher</option>
            </select>
        </div>

        <div>
            <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
            <input type="password" name="password" id="password" required class="w-full rounded-lg border-slate-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-2">Confirm password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required class="w-full rounded-lg border-slate-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
        </div>

        <button type="submit" class="w-full py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition">
            Create account
        </button>

        <p class="text-center text-sm text-slate-500">
            Already registered?
            <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:text-blue-800">Log in</a>
        </p>
    </form>
</div>
@endsection
