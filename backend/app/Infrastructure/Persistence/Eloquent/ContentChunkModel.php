<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentChunkModel extends Model
{
    use HasFactory;

    protected $table = 'content_chunks';

    protected $fillable = [
        'knowledge_base_id',
        'content',
        'embedding_vector',
        'source_type',
        'page_num',
        'section',
        'context_window',
        'metadata',
    ];

    protected $casts = [
        'embedding_vector' => 'array',
        'metadata' => 'array',
        'page_num' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function knowledgeBase(): BelongsTo
    {
        return $this->belongsTo(KnowledgeBaseModel::class, 'knowledge_base_id');
    }
}
