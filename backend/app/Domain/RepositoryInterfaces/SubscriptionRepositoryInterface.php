<?php

declare(strict_types=1);

namespace App\Domain\RepositoryInterfaces;

use App\Domain\Entities\Subscription;
use DateTimeImmutable;

interface SubscriptionRepositoryInterface
{
    public function findById(int $id): ?Subscription;
    
    /**
     * @return Subscription[]
     */
    public function findAll(): array;
    
    /**
     * @return Subscription[]
     */
    public function findByStudentId(int $studentId): array;
    
    /**
     * @return Subscription[]
     */
    public function findActiveByStudentId(int $studentId): array;
    
    public function findActiveByStudentAndKnowledgeBase(int $studentId, int $knowledgeBaseId): ?Subscription;
    
    public function findActiveByStudentAndCourse(int $studentId, int $courseId): ?Subscription;
    
    /**
     * @return Subscription[]
     */
    public function findExpiringBefore(DateTimeImmutable $date): array;
    
    public function save(Subscription $subscription): void;
    
    public function delete(int $id): void;
}
