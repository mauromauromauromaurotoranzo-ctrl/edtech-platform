<?php

declare(strict_types=1);

namespace App\Domain\RepositoryInterfaces;

use App\Domain\Entities\Student;

interface StudentRepositoryInterface
{
    public function findById(int $id): ?Student;
    
    public function findByUserId(int $userId): ?Student;
    
    /**
     * @return Student[]
     */
    public function findAll(): array;
    
    public function save(Student $student): void;
    
    public function delete(int $id): void;
}
