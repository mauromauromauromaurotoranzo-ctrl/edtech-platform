<?php

declare(strict_types=1);

namespace App\Domain\RepositoryInterfaces;

use App\Domain\Entities\Course;
use App\Domain\Entities\CourseStatus;

interface CourseRepositoryInterface
{
    public function findById(int $id): ?Course;
    
    /**
     * @return Course[]
     */
    public function findAll(): array;
    
    /**
     * @return Course[]
     */
    public function findByKnowledgeBaseId(int $knowledgeBaseId): array;
    
    /**
     * @return Course[]
     */
    public function findByStatus(CourseStatus $status): array;
    
    /**
     * @return Course[]
     */
    public function findPublished(): array;
    
    public function save(Course $course): void;
    
    public function delete(int $id): void;
}
