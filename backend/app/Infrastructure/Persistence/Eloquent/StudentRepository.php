<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Entities\Student;
use App\Domain\RepositoryInterfaces\StudentRepositoryInterface;
use App\Domain\ValueObjects\LearningPreferences;

class StudentRepository implements StudentRepositoryInterface
{
    public function __construct(
        private StudentModel $model
    ) {}

    public function findById(int $id): ?Student
    {
        $record = $this->model->find($id);
        return $record ? $this->toEntity($record) : null;
    }

    public function findByUserId(int $userId): ?Student
    {
        $record = $this->model->where('user_id', $userId)->first();
        return $record ? $this->toEntity($record) : null;
    }

    public function findAll(): array
    {
        return $this->model->all()->map(fn($r) => $this->toEntity($r))->toArray();
    }

    public function save(Student $student): void
    {
        $data = [
            'user_id' => $student->getUserId(),
            'learning_preferences' => $student->getLearningPreferences()->toArray(),
        ];

        if ($student->getId()) {
            $this->model->where('id', $student->getId())->update($data);
        } else {
            $this->model->create($data);
        }
    }

    public function delete(int $id): void
    {
        $this->model->destroy($id);
    }

    private function toEntity(StudentModel $model): Student
    {
        return new Student(
            id: $model->id,
            userId: $model->user_id,
            learningPreferences: LearningPreferences::fromArray($model->learning_preferences ?? []),
            createdAt: $model->created_at->toDateTimeImmutable(),
            updatedAt: $model->updated_at->toDateTimeImmutable(),
        );
    }
}
