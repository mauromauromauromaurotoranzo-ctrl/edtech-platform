<?php

declare(strict_types=1);

namespace App\Domain\RepositoryInterfaces;

use App\Domain\Entities\SpacedRepetitionItem;

interface SpacedRepetitionRepositoryInterface
{
    public function findById(int $id): ?SpacedRepetitionItem;
    public function findByStudentId(int $studentId): array;
    public function findByStudentAndChunk(int $studentId, int $contentChunkId): ?SpacedRepetitionItem;
    public function findDueItems(int $studentId): array;
    public function findAllDue(): array;
    public function save(SpacedRepetitionItem $item): void;
    public function delete(int $id): void;
    public function deleteByStudentId(int $studentId): void;
    public function getStats(int $studentId): array;
}
