<?php

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

class ConversationModel extends Model
{
    protected $table = 'conversations';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'student_id',
        'knowledge_base_id',
        'messages',
        'context_retrieval',
        'engagement_score',
        'last_message_at',
    ];

    protected $casts = [
        'messages' => 'array',
        'context_retrieval' => 'array',
        'engagement_score' => 'float',
        'last_message_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(StudentModel::class, 'student_id');
    }

    public function knowledgeBase()
    {
        return $this->belongsTo(KnowledgeBaseModel::class, 'knowledge_base_id');
    }

    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('last_message_at', 'desc')->limit($limit);
    }
}
