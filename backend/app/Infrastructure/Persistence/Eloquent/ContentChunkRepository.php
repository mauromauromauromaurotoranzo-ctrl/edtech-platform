<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Entities\ContentChunk;
use App\Domain\RepositoryInterfaces\ContentChunkRepositoryInterface;

class ContentChunkRepository implements ContentChunkRepositoryInterface
{
    public function __construct(
        private ContentChunkModel $model
    ) {}

    public function findById(int $id): ?ContentChunk
    {
        $record = $this->model->find($id);
        return $record ? $this->toEntity($record) : null;
    }

    public function findAll(): array
    {
        return $this->model->all()->map(fn($r) => $this->toEntity($r))->toArray();
    }

    public function findByKnowledgeBaseId(int $knowledgeBaseId): array
    {
        return $this->model->where('knowledge_base_id', $knowledgeBaseId)
            ->get()
            ->map(fn($r) => $this->toEntity($r))
            ->toArray();
    }

    public function findByKnowledgeBaseIdPaginated(int $knowledgeBaseId, int $page, int $perPage): array
    {
        return $this->model->where('knowledge_base_id', $knowledgeBaseId)
            ->paginate($perPage, ['*'], 'page', $page)
            ->map(fn($r) => $this->toEntity($r))
            ->toArray();
    }

    public function findWithEmbeddingsByKnowledgeBaseId(int $knowledgeBaseId): array
    {
        return $this->model->where('knowledge_base_id', $knowledgeBaseId)
            ->whereNotNull('embedding_vector')
            ->get()
            ->map(fn($r) => $this->toEntity($r))
            ->toArray();
    }

    public function findPendingEmbeddings(int $limit = 100): array
    {
        return $this->model->whereNull('embedding_vector')
            ->limit($limit)
            ->get()
            ->map(fn($r) => $this->toEntity($r))
            ->toArray();
    }

    public function save(ContentChunk $chunk): void
    {
        $data = [
            'knowledge_base_id' => $chunk->getKnowledgeBaseId(),
            'content' => $chunk->getContent(),
            'embedding_vector' => $chunk->getEmbeddingVector(),
            'source_type' => $chunk->getSourceType(),
            'page_num' => $chunk->getPageNum(),
            'section' => $chunk->getSection(),
            'context_window' => $chunk->getContextWindow(),
            'metadata' => $chunk->getMetadata(),
        ];

        if ($chunk->getId()) {
            $this->model->where('id', $chunk->getId())->update($data);
        } else {
            $this->model->create($data);
        }
    }

    public function delete(int $id): void
    {
        $this->model->destroy($id);
    }

    public function deleteByKnowledgeBaseId(int $knowledgeBaseId): void
    {
        $this->model->where('knowledge_base_id', $knowledgeBaseId)->delete();
    }

    private function toEntity(ContentChunkModel $model): ContentChunk
    {
        return new ContentChunk(
            id: $model->id,
            knowledgeBaseId: $model->knowledge_base_id,
            content: $model->content,
            embeddingVector: $model->embedding_vector,
            sourceType: $model->source_type,
            pageNum: $model->page_num,
            section: $model->section,
            contextWindow: $model->context_window,
            metadata: $model->metadata ?? [],
            createdAt: $model->created_at->toDateTimeImmutable(),
            updatedAt: $model->updated_at->toDateTimeImmutable(),
        );
    }
}
