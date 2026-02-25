<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Entities\KnowledgeBase;
use App\Domain\Entities\KnowledgeBaseStatus;
use App\Domain\RepositoryInterfaces\KnowledgeBaseRepositoryInterface;

class KnowledgeBaseRepository implements KnowledgeBaseRepositoryInterface
{
    public function __construct(
        private KnowledgeBaseModel $model
    ) {}

    public function findById(int $id): ?KnowledgeBase
    {
        $record = $this->model->find($id);
        return $record ? $this->toEntity($record) : null;
    }

    public function findBySlug(string $slug): ?KnowledgeBase
    {
        $record = $this->model->where('slug', $slug)->first();
        return $record ? $this->toEntity($record) : null;
    }

    public function findAll(): array
    {
        return $this->model->all()->map(fn($r) => $this->toEntity($r))->toArray();
    }

    public function findByInstructorId(int $instructorId): array
    {
        return $this->model->where('instructor_id', $instructorId)
            ->get()
            ->map(fn($r) => $this->toEntity($r))
            ->toArray();
    }

    public function findByStatus(KnowledgeBaseStatus $status): array
    {
        return $this->model->where('status', $status->value)
            ->get()
            ->map(fn($r) => $this->toEntity($r))
            ->toArray();
    }

    public function findPublic(): array
    {
        return $this->model->where('public_access', true)
            ->where('status', KnowledgeBaseStatus::PUBLISHED->value)
            ->get()
            ->map(fn($r) => $this->toEntity($r))
            ->toArray();
    }

    public function save(KnowledgeBase $knowledgeBase): void
    {
        $data = [
            'instructor_id' => $knowledgeBase->getInstructorId(),
            'title' => $knowledgeBase->getTitle(),
            'description' => $knowledgeBase->getDescription(),
            'slug' => $knowledgeBase->getSlug(),
            'status' => $knowledgeBase->getStatus()->value,
            'public_access' => $knowledgeBase->isPublicAccess(),
            'pricing_model' => $knowledgeBase->getPricingModel(),
            'total_chunks' => $knowledgeBase->getTotalChunks(),
            'last_indexed_at' => $knowledgeBase->getLastIndexedAt(),
        ];

        if ($knowledgeBase->getId()) {
            $this->model->where('id', $knowledgeBase->getId())->update($data);
        } else {
            $this->model->create($data);
        }
    }

    public function delete(int $id): void
    {
        $this->model->destroy($id);
    }

    private function toEntity(KnowledgeBaseModel $model): KnowledgeBase
    {
        return new KnowledgeBase(
            id: $model->id,
            instructorId: $model->instructor_id,
            title: $model->title,
            description: $model->description,
            slug: $model->slug,
            status: KnowledgeBaseStatus::from($model->status),
            publicAccess: $model->public_access,
            pricingModel: $model->pricing_model,
            totalChunks: $model->total_chunks,
            lastIndexedAt: $model->last_indexed_at?->toDateTimeImmutable(),
            createdAt: $model->created_at->toDateTimeImmutable(),
            updatedAt: $model->updated_at->toDateTimeImmutable(),
        );
    }
}
