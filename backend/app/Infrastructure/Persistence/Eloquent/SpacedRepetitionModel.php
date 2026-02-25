<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpacedRepetitionModel extends Model
{
    use HasFactory;

    protected $table = 'spaced_repetition_items';

    protected $fillable = [
        'student_id',
        'content_chunk_id',
        'easiness_factor',
        'repetition_count',
        'interval_days',
        'next_review_at',
        'last_reviewed_at',
        'review_history',
    ];

    protected $casts = [
        'easiness_factor' => 'float',
        'repetition_count' => 'integer',
        'interval_days' => 'integer',
        'next_review_at' => 'datetime',
        'last_reviewed_at' => 'datetime',
        'review_history' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(StudentModel::class, 'student_id');
    }

    public function contentChunk(): BelongsTo
    {
        return $this->belongsTo(ContentChunkModel::class, 'content_chunk_id');
    }
}
