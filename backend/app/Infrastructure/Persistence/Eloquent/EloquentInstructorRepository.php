<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Entities\Instructor;
use App\Domain\RepositoryInterfaces\InstructorRepositoryInterface;
use App\Domain\ValueObjects\Email;

class EloquentInstructorRepository implements InstructorRepositoryInterface
{
    public function findById(string $id): ?Instructor
    {
        $model = InstructorModel::find($id);
        return $model ? $this->toEntity($model) : null;
    }

    public function findByUserId(string $userId): ?Instructor
    {
        $model = InstructorModel::where('user_id', $userId)->first();
        return $model ? $this->toEntity($model) : null;
    }

    public function findByEmail(Email $email): ?Instructor
    {
        $model = InstructorModel::whereHas('user', function ($query) use ($email) {
            $query->where('email', $email->getValue());
        })->first();
        
        return $model ? $this->toEntity($model) : null;
    }

    public function save(Instructor $instructor): void
    {
        InstructorModel::updateOrCreate(
            ['id' => $instructor->getId()],
            [
                'user_id' => $instructor->getUserId(),
                'expertise_areas' => $instructor->getExpertiseAreas(),
                'bio' => $instructor->getBio(),
                'voice_clone_id' => $instructor->getVoiceCloneId(),
                'is_verified' => $instructor->isVerified(),
            ]
        );
    }

    public function delete(string $id): void
    {
        InstructorModel::destroy($id);
    }

    public function findVerified(int $page = 1, int $perPage = 20): array
    {
        $models = InstructorModel::where('is_verified', true)
            ->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $models->map(fn ($model) => $this->toEntity($model))->all(),
            'pagination' => [
                'current_page' => $models->currentPage(),
                'total_pages' => $models->lastPage(),
                'total_items' => $models->total(),
            ],
        ];
    }

    public function findByExpertiseArea(string $area, int $page = 1, int $perPage = 20): array
    {
        $models = InstructorModel::whereJsonContains('expertise_areas', $area)
            ->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $models->map(fn ($model) => $this->toEntity($model))->all(),
            'pagination' => [
                'current_page' => $models->currentPage(),
                'total_pages' => $models->lastPage(),
                'total_items' => $models->total(),
            ],
        ];
    }

    private function toEntity(InstructorModel $model): Instructor
    {
        return new Instructor(
            $model->id,
            $model->user_id,
            new Email($model->user->email),
            $model->user->name,
            $model->expertise_areas ?? [],
            $model->voice_clone_id,
            $model->bio ?? '',
            $model->is_verified,
            $model->created_at
        );
    }
}
