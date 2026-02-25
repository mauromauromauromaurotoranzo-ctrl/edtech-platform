<?php

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

class ContentChunkModel extends Model
{
    protected $table = 'content_chunks';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'knowledge_base_id',
        'content',
        'metadata',
        'embedding',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function knowledgeBase()
    {
        return $this->belongsTo(KnowledgeBaseModel::class, 'knowledge_base_id');
    }

    /**
     * Scope for similarity search using pgvector
     */
    public function scopeSimilarTo($query, array $embedding, int $limit = 5)
    {
        return $query->selectRaw('*, embedding <=> ? as distance', [$embedding])
                     ->orderBy('distance')
                     ->limit($limit);
    }
}
