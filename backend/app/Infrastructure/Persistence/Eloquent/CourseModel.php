<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseModel extends Model
{
    use HasFactory;

    protected $table = 'courses';

    protected $fillable = [
        'knowledge_base_id',
        'title',
        'description',
        'level',
        'self_paced',
        'start_date',
        'end_date',
        'modules',
        'status',
    ];

    protected $casts = [
        'self_paced' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'modules' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function knowledgeBase(): BelongsTo
    {
        return $this->belongsTo(KnowledgeBaseModel::class, 'knowledge_base_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(SubscriptionModel::class, 'course_id');
    }
}
