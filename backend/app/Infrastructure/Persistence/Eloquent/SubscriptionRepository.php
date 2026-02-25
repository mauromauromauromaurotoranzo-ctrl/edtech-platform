<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Entities\Subscription;
use App\Domain\Entities\SubscriptionStatus;
use App\Domain\RepositoryInterfaces\SubscriptionRepositoryInterface;
use DateTimeImmutable;

class SubscriptionRepository implements SubscriptionRepositoryInterface
{
    public function __construct(
        private SubscriptionModel $model
    ) {}

    public function findById(int $id): ?Subscription
    {
        $record = $this->model->find($id);
        return $record ? $this->toEntity($record) : null;
    }

    public function findAll(): array
    {
        return $this->model->all()->map(fn($r) => $this->toEntity($r))->toArray();
    }

    public function findByStudentId(int $studentId): array
    {
        return $this->model->where('student_id', $studentId)
            ->get()
            ->map(fn($r) => $this->toEntity($r))
            ->toArray();
    }

    public function findActiveByStudentId(int $studentId): array
    {
        return $this->model->where('student_id', $studentId)
            ->where('status', SubscriptionStatus::ACTIVE->value)
            ->where('current_period_ends_at', '>', now())
            ->get()
            ->map(fn($r) => $this->toEntity($r))
            ->toArray();
    }

    public function findActiveByStudentAndKnowledgeBase(int $studentId, int $knowledgeBaseId): ?Subscription
    {
        $record = $this->model
            ->where('student_id', $studentId)
            ->where('knowledge_base_id', $knowledgeBaseId)
            ->where('status', SubscriptionStatus::ACTIVE->value)
            ->where('current_period_ends_at', '>', now())
            ->first();
        
        return $record ? $this->toEntity($record) : null;
    }

    public function findActiveByStudentAndCourse(int $studentId, int $courseId): ?Subscription
    {
        $record = $this->model
            ->where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->where('status', SubscriptionStatus::ACTIVE->value)
            ->where('current_period_ends_at', '>', now())
            ->first();
        
        return $record ? $this->toEntity($record) : null;
    }

    public function findExpiringBefore(DateTimeImmutable $date): array
    {
        return $this->model
            ->where('current_period_ends_at', '<', $date)
            ->where('status', SubscriptionStatus::ACTIVE->value)
            ->get()
            ->map(fn($r) => $this->toEntity($r))
            ->toArray();
    }

    public function save(Subscription $subscription): void
    {
        $data = [
            'student_id' => $subscription->getStudentId(),
            'knowledge_base_id' => $subscription->getKnowledgeBaseId(),
            'course_id' => $subscription->getCourseId(),
            'status' => $subscription->getStatus()->value,
            'current_period_starts_at' => $subscription->getCurrentPeriodStartsAt(),
            'current_period_ends_at' => $subscription->getCurrentPeriodEndsAt(),
            'payment_provider_data' => $subscription->getPaymentProviderData(),
        ];

        if ($subscription->getId()) {
            $this->model->where('id', $subscription->getId())->update($data);
        } else {
            $this->model->create($data);
        }
    }

    public function delete(int $id): void
    {
        $this->model->destroy($id);
    }

    private function toEntity(SubscriptionModel $model): Subscription
    {
        return new Subscription(
            id: $model->id,
            studentId: $model->student_id,
            knowledgeBaseId: $model->knowledge_base_id,
            courseId: $model->course_id,
            status: SubscriptionStatus::from($model->status),
            currentPeriodStartsAt: $model->current_period_starts_at->toDateTimeImmutable(),
            currentPeriodEndsAt: $model->current_period_ends_at->toDateTimeImmutable(),
            paymentProviderData: $model->payment_provider_data,
            createdAt: $model->created_at->toDateTimeImmutable(),
            updatedAt: $model->updated_at->toDateTimeImmutable(),
        );
    }
}
