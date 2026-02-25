<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyChallengeModel extends Model
{
    use HasFactory;

    protected $table = 'daily_challenges';

    protected $fillable = [
        'student_id',
        'knowledge_base_id',
        'type',
        'title',
        'content',
        'correct_answer',
        'options',
        'explanation',
        'points',
        'scheduled_for',
        'sent_at',
        'answered_at',
        'student_answer',
        'is_correct',
        'points_earned',
        'metadata',
    ];

    protected $casts = [
        'options' => 'array',
        'metadata' => 'array',
        'points' => 'integer',
        'points_earned' => 'integer',
        'is_correct' => 'boolean',
        'scheduled_for' => 'datetime',
        'sent_at' => 'datetime',
        'answered_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(StudentModel::class, 'student_id');
    }

    public function knowledgeBase(): BelongsTo
    {
        return $this->belongsTo(KnowledgeBaseModel::class, 'knowledge_base_id');
    }
}
