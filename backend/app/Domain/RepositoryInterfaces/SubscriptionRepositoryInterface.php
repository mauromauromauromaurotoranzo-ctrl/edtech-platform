<?php

namespace App\Domain\RepositoryInterfaces;

use App\Domain\Entities\Subscription;

interface SubscriptionRepositoryInterface
{
    public function findById(string $id): ?Subscription;
    public function save(Subscription $subscription): void;
    public function delete(string $id): void;
    public function findByStudentId(string $studentId, int $page = 1, int $perPage = 20): array;
    public function findByKnowledgeBaseId(string $knowledgeBaseId, int $page = 1, int $perPage = 20): array;
    public function findActiveByStudentId(string $studentId): array;
    public function findExpiringSoon(int $days = 7): array;
    public function hasActiveSubscription(string $studentId, string $knowledgeBaseId): bool;
}
