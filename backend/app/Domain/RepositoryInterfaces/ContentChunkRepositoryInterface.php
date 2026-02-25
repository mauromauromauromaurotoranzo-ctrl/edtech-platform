<?php

declare(strict_types=1);

namespace App\Domain\RepositoryInterfaces;

use App\Domain\Entities\ContentChunk;

interface ContentChunkRepositoryInterface
{
    public function findById(int $id): ?ContentChunk;
    
    /**
     * @return ContentChunk[]
     */
    public function findAll(): array;
    
    /**
     * @return ContentChunk[]
     */
    public function findByKnowledgeBaseId(int $knowledgeBaseId): array;
    
    /**
     * @return ContentChunk[]
     */
    public function findByKnowledgeBaseIdPaginated(int $knowledgeBaseId, int $page, int $perPage): array;
    
    /**
     * Find chunks with embeddings for semantic search
     * @return ContentChunk[]
     */
    public function findWithEmbeddingsByKnowledgeBaseId(int $knowledgeBaseId): array;
    
    /**
     * Find chunks without embeddings (pending processing)
     * @return ContentChunk[]
     */
    public function findPendingEmbeddings(int $limit = 100): array;
    
    public function save(ContentChunk $chunk): void;
    
    public function delete(int $id): void;
    
    public function deleteByKnowledgeBaseId(int $knowledgeBaseId): void;
}
