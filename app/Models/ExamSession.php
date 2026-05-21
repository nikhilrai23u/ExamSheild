<?php

namespace App\Models;

use Database\Factories\ExamSessionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class ExamSession extends Model
{
    /** @use HasFactory<ExamSessionFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exam_id',
        'start_time',
        'end_time',
        'status',
        'score',
        'answers',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'answers' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ProctoringLog::class);
    }

    public function expiresAt(): ?Carbon
    {
        if (! $this->start_time || ! $this->exam) {
            return null;
        }

        return $this->start_time->copy()->addMinutes($this->exam->duration_minutes);
    }

    public function remainingSeconds(): int
    {
        $expiresAt = $this->expiresAt();

        if (! $expiresAt) {
            return 0;
        }

        return max(0, now()->diffInSeconds($expiresAt, false));
    }

    public function isExpired(): bool
    {
        return $this->remainingSeconds() === 0;
    }
}
