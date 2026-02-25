<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Entities\Subscription;
use App\Domain\RepositoryInterfaces\SubscriptionRepositoryInterface;
use App\Domain\ValueObjects\Money;

class EloquentSubscriptionRepository implements SubscriptionRepositoryInterface
{
    public function findById(string $id): ?Subscription
    {
        $model = SubscriptionModel::find($id);
        return $model ? $this->toEntity($model) : null;
    }

    public function save(Subscription $subscription): void
    {
        SubscriptionModel::updateOrCreate(
            ['id' => $subscription->getId()],
            [
                'student_id' => $subscription->getStudentId(),
                'knowledge_base_id' => $subscription->getKnowledgeBaseId(),
                'status' => $subscription->getStatus(),
                'amount' => (int) ($subscription->getAmount()->getAmount() * 100),
                'currency' => $subscription->getAmount()->getCurrency(),
                'interval' => $subscription->getInterval(),
                'current_period_starts_at' => $subscription->getCreatedAt(),
                'current_period_ends_at' => $subscription->getCurrentPeriodEndsAt(),
                'payment_provider_subscription_id' => $subscription->getPaymentProviderSubscriptionId(),
            ]
        );
    }

    public function delete(string $id): void
    {
        SubscriptionModel::destroy($id);
    }

    public function findByStudentId(string $studentId, int $page = 1, int $perPage = 20): array
    {
        $models = SubscriptionModel::where('student_id', $studentId)
            ->paginate($perPage, ['*'], 'page', $page);

        return $this->paginateToArray($models);
    }

    public function findByKnowledgeBaseId(string $knowledgeBaseId, int $page = 1, int $perPage = 20): array
    {
        $models = SubscriptionModel::where('knowledge_base_id', $knowledgeBaseId)
            ->paginate($perPage, ['*'], 'page', $page);

        return $this->paginateToArray($models);
    }

    public function findActiveByStudentId(string $studentId): array
    {
        $models = SubscriptionModel::where('student_id', $studentId)
            ->where('status', 'active')
            ->where('current_period_ends_at', '>', now())
            ->get();

        return $models->map(fn ($model) => $this->toEntity($model))->all();
    }

    public function findExpiringSoon(int $days = 7): array
    {
        $models = SubscriptionModel::expiringSoon($days)
            ->where('status', 'active')
            ->get();

        return $models->map(fn ($model) => $this->toEntity($model))->all();
    }

    public function hasActiveSubscription(string $studentId, string $knowledgeBaseId): bool
    {
        return SubscriptionModel::where('student_id', $studentId)
            ->where('knowledge_base_id', $knowledgeBaseId)
            ->where('status', 'active')
            ->where('current_period_ends_at', '>', now())
            ->exists();
    }

    private function toEntity(SubscriptionModel $model): Subscription
    {
        $subscription = new Subscription(
            $model->id,
            $model->student_id,
            $model->knowledge_base_id,
            new Money($model->amount / 100, $model->currency),
            $model->interval,
            $model->current_period_starts_at,
            $model->current_period_ends_at,
            $model->payment_provider_subscription_id,
            $model->created_at
        );

        if ($model->status === 'active') {
            $subscription->activate();
        } elseif ($model->status === 'cancelled') {
            $subscription->cancel();
        }

        return $subscription;
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
