<?php

namespace App\Domain\Entities;

use DateTimeImmutable;
use App\Domain\ValueObjects\Email;

class Instructor
{
    private string $id;
    private string $userId;
    private Email $email;
    private string $name;
    private array $expertiseAreas;
    private ?string $voiceCloneId;
    private string $bio;
    private bool $isVerified;
    private DateTimeImmutable $createdAt;

    public function __construct(
        string $id,
        string $userId,
        Email $email,
        string $name,
        array $expertiseAreas = [],
        ?string $voiceCloneId = null,
        string $bio = '',
        bool $isVerified = false,
        ?DateTimeImmutable $createdAt = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->email = $email;
        $this->name = $name;
        $this->expertiseAreas = $expertiseAreas;
        $this->voiceCloneId = $voiceCloneId;
        $this->bio = $bio;
        $this->isVerified = $isVerified;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getExpertiseAreas(): array
    {
        return $this->expertiseAreas;
    }

    public function getVoiceCloneId(): ?string
    {
        return $this->voiceCloneId;
    }

    public function setVoiceCloneId(string $voiceCloneId): void
    {
        $this->voiceCloneId = $voiceCloneId;
    }

    public function getBio(): string
    {
        return $this->bio;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function verify(): void
    {
        $this->isVerified = true;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
