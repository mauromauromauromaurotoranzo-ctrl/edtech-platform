<?php

declare(strict_types=1);

namespace App\Domain\RepositoryInterfaces;

use App\Domain\Entities\KnowledgeBase;
use App\Domain\Entities\KnowledgeBaseStatus;

interface KnowledgeBaseRepositoryInterface
{
    public function findById(int $id): ?KnowledgeBase;
    
    public function findBySlug(string $slug): ?KnowledgeBase;
    
    /**
     * @return KnowledgeBase[]
     */
    public function findAll(): array;
    
    /**
     * @return KnowledgeBase[]
     */
    public function findByInstructorId(int $instructorId): array;
    
    /**
     * @return KnowledgeBase[]
     */
    public function findByStatus(KnowledgeBaseStatus $status): array;
    
    /**
     * @return KnowledgeBase[]
     */
    public function findPublic(): array;
    
    public function save(KnowledgeBase $knowledgeBase): void;
    
    public function delete(int $id): void;
}
