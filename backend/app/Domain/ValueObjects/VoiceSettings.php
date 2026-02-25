<?php

namespace App\Domain\ValueObjects;

class VoiceSettings
{
    private ?string $voiceCloneId;
    private string $defaultVoice;
    private bool $useInstructorVoice;
    private float $speed;
    private string $language;

    public function __construct(
        ?string $voiceCloneId = null,
        string $defaultVoice = 'alloy',
        bool $useInstructorVoice = true,
        float $speed = 1.0,
        string $language = 'es'
    ) {
        $this->voiceCloneId = $voiceCloneId;
        $this->defaultVoice = $defaultVoice;
        $this->useInstructorVoice = $useInstructorVoice;
        $this->speed = $speed;
        $this->language = $language;
    }

    public function getVoiceCloneId(): ?string
    {
        return $this->voiceCloneId;
    }

    public function getDefaultVoice(): string
    {
        return $this->defaultVoice;
    }

    public function shouldUseInstructorVoice(): bool
    {
        return $this->useInstructorVoice;
    }

    public function getSpeed(): float
    {
        return $this->speed;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function toArray(): array
    {
        return [
            'voice_clone_id' => $this->voiceCloneId,
            'default_voice' => $this->defaultVoice,
            'use_instructor_voice' => $this->useInstructorVoice,
            'speed' => $this->speed,
            'language' => $this->language,
        ];
    }
}
