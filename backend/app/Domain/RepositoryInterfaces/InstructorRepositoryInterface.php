<?php

declare(strict_types=1);

namespace App\Domain\RepositoryInterfaces;

use App\Domain\Entities\Instructor;
use App\Domain\Entities\VerificationStatus;

interface InstructorRepositoryInterface
{
    public function findById(int $id): ?Instructor;
    
    public function findByUserId(int $userId): ?Instructor;
    
    /**
     * @return Instructor[]
     */
    public function findAll(): array;
    
    /**
     * @return Instructor[]
     */
    public function findByStatus(VerificationStatus $status): array;
    
    /**
     * @return Instructor[]
     */
    public function findVerified(): array;
    
    public function save(Instructor $instructor): void;
    
    public function delete(int $id): void;
}
