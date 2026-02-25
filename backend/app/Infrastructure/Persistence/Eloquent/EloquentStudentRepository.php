<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Entities\Student;
use App\Domain\RepositoryInterfaces\StudentRepositoryInterface;
use App\Domain\ValueObjects\Email;

class EloquentStudentRepository implements StudentRepositoryInterface
{
    public function findById(string $id): ?Student
    {
        $model = StudentModel::find($id);
        
        if (!$model) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function findByEmail(Email $email): ?Student
    {
        $model = StudentModel::where('email', $email->getValue())->first();
        
        if (!$model) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function save(Student $student): void
    {
        StudentModel::updateOrCreate(
            ['id' => $student->getId()],
            [
                'email' => $student->getEmail()->getValue(),
                'name' => $student->getName(),
                'preferences' => $student->getPreferences(),
            ]
        );
    }

    public function delete(string $id): void
    {
        StudentModel::destroy($id);
    }

    public function findAll(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $query = StudentModel::query();

        if (isset($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        $models = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $models->map(fn ($model) => $this->toEntity($model))->all(),
            'pagination' => [
                'current_page' => $models->currentPage(),
                'total_pages' => $models->lastPage(),
                'total_items' => $models->total(),
                'per_page' => $models->perPage(),
            ],
        ];
    }

    private function toEntity(StudentModel $model): Student
    {
        return new Student(
            $model->id,
            new Email($model->email),
            $model->name,
            $model->preferences ?? [],
            $model->created_at
        );
    }
}
