<?php

declare(strict_types=1);

namespace App\Domain\RepositoryInterfaces;

use App\Domain\Entities\SmartReminder;
use DateTimeImmutable;

interface SmartReminderRepositoryInterface
{
    public function findById(int $id): ?SmartReminder;
    public function findAll(): array;
    public function findByStudentId(int $studentId): array;
    public function findActiveByStudentId(int $studentId): array;
    public function findDueReminders(DateTimeImmutable $before): array;
    public function findByType(string $type): array;
    public function findByKnowledgeBaseId(int $knowledgeBaseId): array;
    public function save(SmartReminder $reminder): void;
    public function delete(int $id): void;
    public function deleteByStudentId(int $studentId): void;
}
