<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Entities\Course;
use App\Domain\Entities\CourseLevel;
use App\Domain\Entities\CourseStatus;
use App\Domain\Entities\Lesson;
use App\Domain\Entities\Module;
use App\Domain\RepositoryInterfaces\CourseRepositoryInterface;

class CourseRepository implements CourseRepositoryInterface
{
    public function __construct(
        private CourseModel $model
    ) {}

    public function findById(int $id): ?Course
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

    public function findByStatus(CourseStatus $status): array
    {
        return $this->model->where('status', $status->value)
            ->get()
            ->map(fn($r) => $this->toEntity($r))
            ->toArray();
    }

    public function findPublished(): array
    {
        return $this->findByStatus(CourseStatus::PUBLISHED);
    }

    public function save(Course $course): void
    {
        $data = [
            'knowledge_base_id' => $course->getKnowledgeBaseId(),
            'title' => $course->getTitle(),
            'description' => $course->getDescription(),
            'level' => $course->getLevel()->value,
            'self_paced' => $course->isSelfPaced(),
            'start_date' => $course->getStartDate(),
            'end_date' => $course->getEndDate(),
            'modules' => array_map(fn(Module $m) => $m->toArray(), $course->getModules()),
            'status' => $course->getStatus()->value,
        ];

        if ($course->getId()) {
            $this->model->where('id', $course->getId())->update($data);
        } else {
            $this->model->create($data);
        }
    }

    public function delete(int $id): void
    {
        $this->model->destroy($id);
    }

    private function toEntity(CourseModel $model): Course
    {
        $modules = array_map(
            fn(array $m) => new Module(
                id: $m['id'],
                title: $m['title'],
                description: $m['description'] ?? null,
                order: $m['order'],
                lessons: array_map(
                    fn(array $l) => new Lesson(
                        id: $l['id'],
                        title: $l['title'],
                        content: $l['content'] ?? null,
                        type: $l['type'],
                        order: $l['order'],
                        durationMinutes: $l['duration_minutes'] ?? null,
                    ),
                    $m['lessons'] ?? []
                ),
            ),
            $model->modules ?? []
        );

        return new Course(
            id: $model->id,
            knowledgeBaseId: $model->knowledge_base_id,
            title: $model->title,
            description: $model->description,
            level: CourseLevel::from($model->level),
            selfPaced: $model->self_paced,
            startDate: $model->start_date?->toDateTimeImmutable(),
            endDate: $model->end_date?->toDateTimeImmutable(),
            modules: $modules,
            status: CourseStatus::from($model->status),
            createdAt: $model->created_at->toDateTimeImmutable(),
            updatedAt: $model->updated_at->toDateTimeImmutable(),
        );
    }
}
