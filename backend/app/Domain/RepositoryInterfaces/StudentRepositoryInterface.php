<?php

namespace App\Domain\RepositoryInterfaces;

use App\Domain\Entities\Student;
use App\Domain\ValueObjects\Email;

interface StudentRepositoryInterface
{
    public function findById(string $id): ?Student;
    public function findByEmail(Email $email): ?Student;
    public function save(Student $student): void;
    public function delete(string $id): void;
    public function findAll(array $filters = [], int $page = 1, int $perPage = 20): array;
}
