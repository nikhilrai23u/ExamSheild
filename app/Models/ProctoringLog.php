<?php

namespace App\Models;

use Database\Factories\ProctoringLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProctoringLog extends Model
{
    /** @use HasFactory<ProctoringLogFactory> */
    use HasFactory;

    protected $fillable = [
        'exam_session_id',
        'event_type',
        'event_timestamp',
        'metadata',
    ];

    protected $casts = [
        'event_timestamp' => 'datetime',
        'metadata' => 'array',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class, 'exam_session_id');
    }
}
