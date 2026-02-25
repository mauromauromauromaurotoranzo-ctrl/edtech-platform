<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use DateTimeImmutable;

class InstructorVoice
{
    public function __construct(
        private ?int $id,
        private int $instructorId,
        private string $voiceId,
        private string $name,
        private ?string $description,
        private VoiceSettings $settings,
        private ?string $sampleUrl,
        private bool $isDefault,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
    ) {}

    public static function create(
        int $instructorId,
        string $voiceId,
        string $name,
        ?string $description = null,
        ?VoiceSettings $settings = null,
        ?string $sampleUrl = null,
        bool $isDefault = false,
    ): self {
        $now = new DateTimeImmutable();
        return new self(
            id: null,
            instructorId: $instructorId,
            voiceId: $voiceId,
            name: $name,
            description: $description,
            settings: $settings ?? new VoiceSettings(),
            sampleUrl: $sampleUrl,
            isDefault: $isDefault,
            createdAt: $now,
            updatedAt: $now,
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInstructorId(): int
    {
        return $this->instructorId;
    }

    public function getVoiceId(): string
    {
        return $this->voiceId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getSettings(): VoiceSettings
    {
        return $this->settings;
    }

    public function getSampleUrl(): ?string
    {
        return $this->sampleUrl;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function updateSettings(VoiceSettings $settings): void
    {
        $this->settings = $settings;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function setAsDefault(bool $default = true): void
    {
        $this->isDefault = $default;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function updateMetadata(string $name, ?string $description): void
    {
        $this->name = $name;
        $this->description = $description;
        $this->updatedAt = new DateTimeImmutable();
    }
}
