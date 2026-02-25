<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionModel extends Model
{
    use HasFactory;

    protected $table = 'subscriptions';

    protected $fillable = [
        'student_id',
        'knowledge_base_id',
        'course_id',
        'status',
        'current_period_starts_at',
        'current_period_ends_at',
        'payment_provider_data',
    ];

    protected $casts = [
        'current_period_starts_at' => 'datetime',
        'current_period_ends_at' => 'datetime',
        'payment_provider_data' => 'array',
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

    public function course(): BelongsTo
    {
        return $this->belongsTo(CourseModel::class, 'course_id');
    }
}
