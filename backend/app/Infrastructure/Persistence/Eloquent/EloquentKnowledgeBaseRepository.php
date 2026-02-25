<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Entities\KnowledgeBase;
use App\Domain\RepositoryInterfaces\KnowledgeBaseRepositoryInterface;

class EloquentKnowledgeBaseRepository implements KnowledgeBaseRepositoryInterface
{
    public function findById(string $id): ?KnowledgeBase
    {
        $model = KnowledgeBaseModel::find($id);
        return $model ? $this->toEntity($model) : null;
    }

    public function findBySlug(string $slug): ?KnowledgeBase
    {
        $model = KnowledgeBaseModel::where('slug', $slug)->first();
        return $model ? $this->toEntity($model) : null;
    }

    public function save(KnowledgeBase $knowledgeBase): void
    {
        KnowledgeBaseModel::updateOrCreate(
            ['id' => $knowledgeBase->getId()],
            [
                'instructor_id' => $knowledgeBase->getInstructorId(),
                'title' => $knowledgeBase->getTitle(),
                'description' => $knowledgeBase->getDescription(),
                'slug' => $knowledgeBase->getSlug(),
                'status' => $knowledgeBase->getStatus(),
                'settings' => $knowledgeBase->getSettings(),
            ]
        );
    }

    public function delete(string $id): void
    {
        KnowledgeBaseModel::destroy($id);
    }

    public function findByInstructorId(string $instructorId, int $page = 1, int $perPage = 20): array
    {
        $models = KnowledgeBaseModel::where('instructor_id', $instructorId)
            ->paginate($perPage, ['*'], 'page', $page);

        return $this->paginateToArray($models);
    }

    public function findPublished(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $query = KnowledgeBaseModel::where('status', 'published');

        if (isset($filters['instructor_id'])) {
            $query->where('instructor_id', $filters['instructor_id']);
        }

        $models = $query->paginate($perPage, ['*'], 'page', $page);

        return $this->paginateToArray($models);
    }

    public function search(string $query, int $page = 1, int $perPage = 20): array
    {
        $models = KnowledgeBaseModel::where(function ($q) use ($query) {
            $q->where('title', 'ILIKE', "%{$query}%")
              ->orWhere('description', 'ILIKE', "%{$query}%");
        })
        ->where('status', 'published')
        ->paginate($perPage, ['*'], 'page', $page);

        return $this->paginateToArray($models);
    }

    private function toEntity(KnowledgeBaseModel $model): KnowledgeBase
    {
        $kb = new KnowledgeBase(
            $model->id,
            $model->instructor_id,
            $model->title,
            $model->description,
            $model->slug,
            $model->status,
            $model->settings ?? [],
            $model->created_at
        );

        if ($model->last_indexed_at) {
            $kb->markAsIndexed();
        }

        return $kb;
    }

    private function paginateToArray($models): array
    {
        return [
            'data' => $models->map(fn ($model) => $this->toEntity($model))->all(),
            'pagination' => [
                'current_page' => $models->currentPage(),
                'total_pages' => $models->lastPage(),
                'total_items' => $models->total(),
            ],
        ];
    }
}
