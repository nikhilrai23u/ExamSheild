<?php

namespace App\Http\Controllers;

use App\Models\ExamSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProctoringController extends Controller
{
    private const SecurityViolationEvents = [
        'tab_switch',
        'window_blur',
        'fullscreen_exit',
        'copy_attempt',
        'paste_attempt',
        'context_menu',
        'print_attempt',
        'keyboard_shortcut',
        'developer_tools_suspected',
    ];

    public function log(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'exam_session_id' => ['required', 'exists:exam_sessions,id'],
            'event_type' => [
                'required',
                'string',
                Rule::in([
                    'session_started',
                    'fullscreen_entered',
                    'tab_switch',
                    'window_blur',
                    'fullscreen_exit',
                    'copy_attempt',
                    'paste_attempt',
                    'context_menu',
                    'print_attempt',
                    'keyboard_shortcut',
                    'developer_tools_suspected',
                    'heartbeat',
                ]),
            ],
            'metadata' => ['nullable', 'array'],
        ]);

        $session = ExamSession::query()->findOrFail($validated['exam_session_id']);

        if ($session->user_id !== $request->user()->id || $session->status !== 'active') {
            abort(403);
        }

        $metadata = $validated['metadata'] ?? [];
        $metadata['ip_address'] = $request->ip();
        $metadata['user_agent'] = $request->userAgent();

        $session->logs()->create([
            'event_type' => $validated['event_type'],
            'metadata' => $metadata,
        ]);

        $violationCount = $session->logs()
            ->whereIn('event_type', self::SecurityViolationEvents)
            ->count();

        if (in_array($validated['event_type'], self::SecurityViolationEvents, true) && $violationCount >= 3) {
            $session->update([
                'end_time' => now(),
                'status' => 'cancelled',
                'score' => 0,
            ]);

            $session->logs()->create([
                'event_type' => 'exam_cancelled',
                'metadata' => [
                    'reason' => '3 security violations were detected.',
                    'violation_count' => $violationCount,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ],
            ]);

            return response()->json([
                'status' => 'cancelled',
                'message' => 'Exam cancelled because 3 security violations were detected.',
                'redirect_url' => route('student.results', $session),
                'security_violations' => $violationCount,
            ]);
        }

        return response()->json([
            'status' => 'logged',
            'security_violations' => $violationCount,
        ]);
    }
}
