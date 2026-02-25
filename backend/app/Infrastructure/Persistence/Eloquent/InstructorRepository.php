<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Entities\Instructor;
use App\Domain\Entities\VerificationStatus;
use App\Domain\RepositoryInterfaces\InstructorRepositoryInterface;

class InstructorRepository implements InstructorRepositoryInterface
{
    public function __construct(
        private InstructorModel $model
    ) {}

    public function findById(int $id): ?Instructor
    {
        $record = $this->model->find($id);
        return $record ? $this->toEntity($record) : null;
    }

    public function findByUserId(int $userId): ?Instructor
    {
        $record = $this->model->where('user_id', $userId)->first();
        return $record ? $this->toEntity($record) : null;
    }

    public function findAll(): array
    {
        return $this->model->all()->map(fn($r) => $this->toEntity($r))->toArray();
    }

    public function findByStatus(VerificationStatus $status): array
    {
        return $this->model->where('verification_status', $status->value)
            ->get()
            ->map(fn($r) => $this->toEntity($r))
            ->toArray();
    }

    public function findVerified(): array
    {
        return $this->findByStatus(VerificationStatus::VERIFIED);
    }

    public function save(Instructor $instructor): void
    {
        $data = [
            'user_id' => $instructor->getUserId(),
            'expertise_areas' => $instructor->getExpertiseAreas(),
            'verification_status' => $instructor->getVerificationStatus()->value,
            'stripe_account_id' => $instructor->getStripeAccountId(),
        ];

        if ($instructor->getId()) {
            $this->model->where('id', $instructor->getId())->update($data);
        } else {
            $this->model->create($data);
        }
    }

    public function delete(int $id): void
    {
        $this->model->destroy($id);
    }

    private function toEntity(InstructorModel $model): Instructor
    {
        return new Instructor(
            id: $model->id,
            userId: $model->user_id,
            expertiseAreas: $model->expertise_areas ?? [],
            verificationStatus: VerificationStatus::from($model->verification_status),
            stripeAccountId: $model->stripe_account_id,
            createdAt: $model->created_at->toDateTimeImmutable(),
            updatedAt: $model->updated_at->toDateTimeImmutable(),
        );
    }
}
