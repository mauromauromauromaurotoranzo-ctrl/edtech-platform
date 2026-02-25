<?php

declare(strict_types=1);

namespace App\Domain\RepositoryInterfaces;

use App\Domain\Entities\StudentProgress;

interface StudentProgressRepositoryInterface
{
    public function findById(int $id): ?StudentProgress;
    public function findByStudentId(int $studentId): array;
    public function findByStudentAndKnowledgeBase(int $studentId, int $knowledgeBaseId): ?StudentProgress;
    public function findTopStudents(int $knowledgeBaseId, int $limit = 10): array;
    public function save(StudentProgress $progress): void;
    public function delete(int $id): void;
    public function deleteByStudentId(int $studentId): void;
    public function getLeaderboard(int $knowledgeBaseId, int $limit = 20): array;
}
