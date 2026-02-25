<?php

namespace App\Domain\Entities;

use DateTimeImmutable;
use App\Domain\ValueObjects\Email;

class Student
{
    private string $id;
    private Email $email;
    private string $name;
    private array $preferences;
    private DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt;

    public function __construct(
        string $id,
        Email $email,
        string $name,
        array $preferences = [],
        ?DateTimeImmutable $createdAt = null
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->name = $name;
        $this->preferences = $preferences;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->updatedAt = null;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPreferences(): array
    {
        return $this->preferences;
    }

    public function updatePreferences(array $preferences): void
    {
        $this->preferences = $preferences;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
