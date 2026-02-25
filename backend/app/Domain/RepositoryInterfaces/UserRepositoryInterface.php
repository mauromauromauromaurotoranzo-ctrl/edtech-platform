<?php

declare(strict_types=1);

namespace App\Domain\RepositoryInterfaces;

use App\Domain\Entities\User;
use App\Domain\ValueObjects\Email;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;
    
    public function findByEmail(Email $email): ?User;
    
    /**
     * @return User[]
     */
    public function findAll(): array;
    
    /**
     * @return User[]
     */
    public function findByRole(string $role): array;
    
    public function save(User $user): void;
    
    public function delete(int $id): void;
    
    public function existsByEmail(Email $email): bool;
}
