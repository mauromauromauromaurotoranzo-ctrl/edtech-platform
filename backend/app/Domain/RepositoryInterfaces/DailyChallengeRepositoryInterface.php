<?php

declare(strict_types=1);

namespace App\Domain\RepositoryInterfaces;

use App\Domain\Entities\DailyChallenge;
use DateTimeImmutable;

interface DailyChallengeRepositoryInterface
{
    public function findById(int $id): ?DailyChallenge;
    public function findAll(): array;
    public function findByStudentId(int $studentId): array;
    public function findByStudentIdAndDate(int $studentId, DateTimeImmutable $date): ?DailyChallenge;
    public function findPendingByStudentId(int $studentId): array;
    public function findPendingForToday(): array;
    public function findByKnowledgeBaseId(int $knowledgeBaseId): array;
    public function findAnsweredByStudentId(int $studentId, int $limit = 10): array;
    public function save(DailyChallenge $challenge): void;
    public function delete(int $id): void;
    public function deleteByStudentId(int $studentId): void;
    public function countByStudentId(int $studentId): int;
    public function countCorrectByStudentId(int $studentId): int;
}
