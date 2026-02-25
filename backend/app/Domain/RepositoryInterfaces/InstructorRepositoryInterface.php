<?php

namespace App\Domain\RepositoryInterfaces;

use App\Domain\Entities\Instructor;
use App\Domain\ValueObjects\Email;

interface InstructorRepositoryInterface
{
    public function findById(string $id): ?Instructor;
    public function findByUserId(string $userId): ?Instructor;
    public function findByEmail(Email $email): ?Instructor;
    public function save(Instructor $instructor): void;
    public function delete(string $id): void;
    public function findVerified(int $page = 1, int $perPage = 20): array;
    public function findByExpertiseArea(string $area, int $page = 1, int $perPage = 20): array;
}
