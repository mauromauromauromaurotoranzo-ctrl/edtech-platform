<?php

namespace App\Domain\RepositoryInterfaces;

use App\Domain\Entities\KnowledgeBase;

interface KnowledgeBaseRepositoryInterface
{
    public function findById(string $id): ?KnowledgeBase;
    public function findBySlug(string $slug): ?KnowledgeBase;
    public function save(KnowledgeBase $knowledgeBase): void;
    public function delete(string $id): void;
    public function findByInstructorId(string $instructorId, int $page = 1, int $perPage = 20): array;
    public function findPublished(array $filters = [], int $page = 1, int $perPage = 20): array;
    public function search(string $query, int $page = 1, int $perPage = 20): array;
}
