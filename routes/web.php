<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExamSessionController;
use App\Http\Controllers\ProctoringController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'createLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'storeLogin']);
    Route::get('/register', [AuthController::class, 'createRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'storeRegister']);
});

Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

Route::get('/dashboard', function () {
    if (auth()->user()->role === 'teacher') {
        return redirect()->route('exams.index');
    }

    return redirect()->route('student.dashboard');
})->middleware('auth')->name('dashboard');

Route::middleware(['auth', 'role:teacher'])->group(function () {
    Route::resource('exams', ExamController::class);
    Route::get('/exams/{exam}/results', [ExamController::class, 'results'])->name('exams.results');
    Route::get('/sessions/{session}/logs', [ExamController::class, 'sessionLogs'])->name('sessions.logs');
    Route::resource('exams.questions', QuestionController::class)->only(['store']);
    Route::delete('/questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');
});

Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/student/dashboard', [ExamSessionController::class, 'index'])->name('student.dashboard');
    Route::post('/exams/{exam}/start', [ExamSessionController::class, 'start'])->name('exams.start');
    Route::get('/exams/{exam}/take/{session}', [ExamSessionController::class, 'take'])->name('exams.take');
    Route::post('/exams/{exam}/submit/{session}', [ExamSessionController::class, 'submit'])->name('exams.submit');
    Route::get('/student/results/{session}', [ExamSessionController::class, 'results'])->name('student.results');
});

Route::post('/proctoring/log', [ProctoringController::class, 'log'])->middleware('auth')->name('proctoring.log');

Route::resource('students', StudentController::class)->except(['show'])->middleware(['auth', 'role:teacher']);
