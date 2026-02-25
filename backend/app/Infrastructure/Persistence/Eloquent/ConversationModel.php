<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversationModel extends Model
{
    use HasFactory;

    protected $table = 'conversations';

    protected $fillable = [
        'student_id',
        'knowledge_base_id',
        'messages',
        'chunks_referenced',
        'engagement_score',
    ];

    protected $casts = [
        'messages' => 'array',
        'chunks_referenced' => 'array',
        'engagement_score' => 'float',
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
