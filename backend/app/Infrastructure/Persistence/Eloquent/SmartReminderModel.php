<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmartReminderModel extends Model
{
    use HasFactory;

    protected $table = 'smart_reminders';

    protected $fillable = [
        'student_id',
        'knowledge_base_id',
        'type',
        'title',
        'message',
        'scheduled_at',
        'is_recurring',
        'recurrence_pattern',
        'priority',
        'is_active',
        'sent_at',
        'send_count',
        'metadata',
    ];

    protected $casts = [
        'is_recurring' => 'boolean',
        'is_active' => 'boolean',
        'priority' => 'float',
        'send_count' => 'integer',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'metadata' => 'array',
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
