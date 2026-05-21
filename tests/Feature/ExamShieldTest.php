<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;

uses(DatabaseMigrations::class);

use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\Question;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('simple exam and question creation', function () {
    $exam = Exam::create([
        'title' => 'Simple',
        'duration_minutes' => 60,
    ]);

    Question::create([
        'exam_id' => $exam->id,
        'question_text' => 'What is 1+1?',
        'type' => 'mcq',
        'correct_answer' => '2',
        'points' => 1,
    ]);

    $this->assertDatabaseHas('exams', ['title' => 'Simple']);
    $this->assertDatabaseHas('questions', ['question_text' => 'What is 1+1?']);
});

test('teacher can access exam index', function () {
    $teacher = User::factory()->create(['role' => 'teacher']);

    $response = $this->actingAs($teacher)->get(route('exams.index'));

    $response->assertStatus(200);
});

test('student cannot access exam index', function () {
    $student = User::factory()->create(['role' => 'student']);

    $response = $this->actingAs($student)->get(route('exams.index'));

    $response->assertStatus(403);
});

test('teacher can create an exam', function () {
    $this->withoutExceptionHandling();
    $teacher = User::factory()->create(['role' => 'teacher']);

    $response = $this->actingAs($teacher)->post(route('exams.store'), [
        'title' => 'Test Exam',
        'duration_minutes' => 30,
    ]);

    $this->assertDatabaseHas('exams', ['title' => 'Test Exam']);
    $exam = Exam::where('title', 'Test Exam')->firstOrFail();
    $response->assertRedirect(route('exams.show', $exam));
});

test('teacher can add a question to an exam', function () {
    $teacher = User::factory()->create(['role' => 'teacher']);
    $exam = Exam::factory()->create();

    $response = $this->actingAs($teacher)->post(route('exams.questions.store', $exam), [
        'question_text' => 'What is 1+1?',
        'type' => 'mcq',
        'options' => ['1', '2', '3', '4'],
        'correct_answer' => '1',
        'points' => 1,
    ]);

    $this->assertDatabaseHas('questions', [
        'question_text' => 'What is 1+1?',
        'correct_answer' => '1',
    ]);
    $response->assertRedirect(route('exams.show', $exam));
});

test('teacher must add exactly four mcq options and one correct answer', function () {
    $teacher = User::factory()->create(['role' => 'teacher']);
    $exam = Exam::factory()->create();

    $this->actingAs($teacher)->post(route('exams.questions.store', $exam), [
        'question_text' => 'What is 2+2?',
        'type' => 'mcq',
        'options' => ['3', '4'],
        'correct_answer' => '1',
        'points' => 1,
    ])->assertSessionHasErrors('options');

    $this->actingAs($teacher)->post(route('exams.questions.store', $exam), [
        'question_text' => 'What is 2+2?',
        'type' => 'mcq',
        'options' => ['1', '2', '3', '4'],
        'correct_answer' => '5',
        'points' => 1,
    ])->assertSessionHasErrors('correct_answer');
});

test('teacher sees exam analytics in results panel', function () {
    $teacher = User::factory()->create(['role' => 'teacher']);
    $exam = Exam::factory()->create();
    Question::factory()->create(['exam_id' => $exam->id, 'points' => 10]);

    ExamSession::factory()->create([
        'exam_id' => $exam->id,
        'status' => 'completed',
        'score' => 8,
    ]);
    $cancelledSession = ExamSession::factory()->create([
        'exam_id' => $exam->id,
        'status' => 'cancelled',
        'score' => 0,
    ]);
    $cancelledSession->logs()->create([
        'event_type' => 'tab_switch',
        'metadata' => ['reason' => 'Testing'],
    ]);

    $this->actingAs($teacher)
        ->get(route('exams.results', $exam))
        ->assertOk()
        ->assertSee('Attempts')
        ->assertSee('Avg Score')
        ->assertSee('High Score')
        ->assertSee('Security Violations');
});

test('student can start an exam session', function () {
    $student = User::factory()->create(['role' => 'student']);
    $exam = Exam::factory()->create();
    Question::factory()->create(['exam_id' => $exam->id]);

    $response = $this->actingAs($student)->post(route('exams.start', $exam));

    $this->assertDatabaseHas('exam_sessions', [
        'user_id' => $student->id,
        'exam_id' => $exam->id,
        'status' => 'active',
    ]);
});

test('student dashboard shows progress stats', function () {
    $student = User::factory()->create(['role' => 'student']);
    $availableExam = Exam::factory()->create();
    Question::factory()->create(['exam_id' => $availableExam->id]);
    ExamSession::factory()->create([
        'user_id' => $student->id,
        'status' => 'completed',
        'score' => 4,
    ]);
    ExamSession::factory()->create([
        'user_id' => $student->id,
        'status' => 'cancelled',
        'score' => 0,
    ]);

    $this->actingAs($student)
        ->get(route('student.dashboard'))
        ->assertOk()
        ->assertSee('Available')
        ->assertSee('Attempts')
        ->assertSee('Completed')
        ->assertSee('Cancelled');
});

test('student sees result panel after completing exam', function () {
    $student = User::factory()->create(['role' => 'student']);
    $exam = Exam::factory()->create();
    $question = Question::factory()->create([
        'exam_id' => $exam->id,
        'options' => ['1', '2', '3', '4'],
        'correct_answer' => '1',
        'points' => 5,
    ]);
    $session = ExamSession::factory()->create([
        'user_id' => $student->id,
        'exam_id' => $exam->id,
        'status' => 'active',
    ]);

    $response = $this->actingAs($student)->post(route('exams.submit', [$exam, $session]), [
        'answers' => [
            $question->id => '1',
        ],
    ]);

    $response->assertRedirect(route('student.results', $session));
    $this->assertDatabaseHas('exam_sessions', [
        'id' => $session->id,
        'status' => 'completed',
        'score' => 5,
    ]);

    $session->refresh();
    expect($session->answers)->toHaveCount(1)
        ->and($session->answers[0]['is_correct'])->toBeTrue()
        ->and($session->answers[0]['student_answer_label'])->toBe('2')
        ->and($session->answers[0]['correct_answer_label'])->toBe('2')
        ->and($session->answers[0]['earned_points'])->toBe(5);

    $this->actingAs($student)
        ->get(route('student.results', $session))
        ->assertOk()
        ->assertSee('Result Panel')
        ->assertSee('5 / 5')
        ->assertSee('Question Review')
        ->assertSee('Right answer');
});

test('answer review shows incorrect student answer with right answer', function () {
    $student = User::factory()->create(['role' => 'student']);
    $exam = Exam::factory()->create();
    $question = Question::factory()->create([
        'exam_id' => $exam->id,
        'options' => ['Paris', 'Berlin', 'Rome', 'Madrid'],
        'correct_answer' => '0',
        'points' => 3,
    ]);
    $session = ExamSession::factory()->create([
        'user_id' => $student->id,
        'exam_id' => $exam->id,
        'status' => 'active',
    ]);

    $this->actingAs($student)->post(route('exams.submit', [$exam, $session]), [
        'answers' => [
            $question->id => '1',
        ],
    ]);

    $session->refresh();

    expect($session->answers[0]['is_correct'])->toBeFalse()
        ->and($session->answers[0]['student_answer_label'])->toBe('Berlin')
        ->and($session->answers[0]['correct_answer_label'])->toBe('Paris')
        ->and($session->answers[0]['earned_points'])->toBe(0);

    $this->actingAs($student)
        ->get(route('student.results', $session))
        ->assertOk()
        ->assertSee('Incorrect')
        ->assertSee('Berlin')
        ->assertSee('Paris');
});

test('student must provide exam access code when required', function () {
    $student = User::factory()->create(['role' => 'student']);
    $exam = Exam::factory()->create(['password' => Hash::make('open-sesame')]);
    Question::factory()->create(['exam_id' => $exam->id]);

    $this->actingAs($student)
        ->post(route('exams.start', $exam), ['password' => 'wrong-code'])
        ->assertSessionHasErrors('password');

    $this->actingAs($student)
        ->post(route('exams.start', $exam), ['password' => 'open-sesame'])
        ->assertRedirect();

    $this->assertDatabaseHas('exam_sessions', [
        'user_id' => $student->id,
        'exam_id' => $exam->id,
        'status' => 'active',
    ]);
});

test('proctoring events can be logged', function () {
    $student = User::factory()->create(['role' => 'student']);
    $session = ExamSession::factory()->create(['user_id' => $student->id]);

    $response = $this->actingAs($student)->postJson(route('proctoring.log'), [
        'exam_session_id' => $session->id,
        'event_type' => 'tab_switch',
        'metadata' => ['reason' => 'Testing'],
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('proctoring_logs', [
        'exam_session_id' => $session->id,
        'event_type' => 'tab_switch',
    ]);
});

test('student cannot log proctoring events for another student session', function () {
    $student = User::factory()->create(['role' => 'student']);
    $otherStudent = User::factory()->create(['role' => 'student']);
    $session = ExamSession::factory()->create(['user_id' => $otherStudent->id]);

    $this->actingAs($student)->postJson(route('proctoring.log'), [
        'exam_session_id' => $session->id,
        'event_type' => 'tab_switch',
        'metadata' => ['reason' => 'Testing'],
    ])->assertForbidden();

    $this->assertDatabaseMissing('proctoring_logs', [
        'exam_session_id' => $session->id,
        'event_type' => 'tab_switch',
    ]);
});

test('exam is cancelled on the third security violation', function () {
    $student = User::factory()->create(['role' => 'student']);
    $session = ExamSession::factory()->create(['user_id' => $student->id]);

    $events = ['tab_switch', 'copy_attempt', 'fullscreen_exit'];

    foreach ($events as $index => $eventType) {
        $response = $this->actingAs($student)->postJson(route('proctoring.log'), [
            'exam_session_id' => $session->id,
            'event_type' => $eventType,
            'metadata' => ['reason' => "Violation {$index}"],
        ])->assertOk();

        if ($index < 2) {
            $response->assertJsonPath('status', 'logged')
                ->assertJsonPath('security_violations', $index + 1);
        } else {
            $response->assertJsonPath('status', 'cancelled')
                ->assertJsonPath('security_violations', 3);
        }
    }

    $this->assertDatabaseHas('exam_sessions', [
        'id' => $session->id,
        'status' => 'cancelled',
        'score' => 0,
    ]);

    $this->assertDatabaseHas('proctoring_logs', [
        'exam_session_id' => $session->id,
        'event_type' => 'exam_cancelled',
    ]);
});
