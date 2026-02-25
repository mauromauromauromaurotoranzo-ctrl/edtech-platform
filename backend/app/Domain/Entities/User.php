<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\ValueObjects\Email;
use DateTimeImmutable;

class User
{
    public function __construct(
        private ?int $id,
        private Email $email,
        private string $passwordHash,
        private UserRole $role,
        private string $name,
        private ?string $avatar = null,
        private ?string $bio = null,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
    ) {}

    public static function create(
        Email $email,
        string $passwordHash,
        UserRole $role,
        string $name,
        ?string $avatar = null,
        ?string $bio = null,
    ): self {
        $now = new DateTimeImmutable();
        return new self(
            id: null,
            email: $email,
            passwordHash: $passwordHash,
            role: $role,
            name: $name,
            avatar: $avatar,
            bio: $bio,
            createdAt: $now,
            updatedAt: $now,
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getRole(): UserRole
    {
        return $this->role;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function updateProfile(string $name, ?string $bio): void
    {
        $this->name = $name;
        $this->bio = $bio;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function updateAvatar(string $avatar): void
    {
        $this->avatar = $avatar;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function isInstructor(): bool
    {
        return $this->role === UserRole::INSTRUCTOR;
    }

    public function isStudent(): bool
    {
        return $this->role === UserRole::STUDENT;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }
}
