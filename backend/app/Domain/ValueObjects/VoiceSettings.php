<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

final class VoiceSettings
{
    public function __construct(
        private ?string $voiceId = null,
        private float $stability = 0.5,
        private float $similarityBoost = 0.75,
        private float $style = 0.0,
        private bool $useSpeakerBoost = true,
    ) {
        $this->validateRange('stability', $stability, 0.0, 1.0);
        $this->validateRange('similarityBoost', $similarityBoost, 0.0, 1.0);
        $this->validateRange('style', $style, 0.0, 1.0);
    }

    public function getVoiceId(): ?string
    {
        return $this->voiceId;
    }

    public function getStability(): float
    {
        return $this->stability;
    }

    public function getSimilarityBoost(): float
    {
        return $this->similarityBoost;
    }

    public function getStyle(): float
    {
        return $this->style;
    }

    public function useSpeakerBoost(): bool
    {
        return $this->useSpeakerBoost;
    }

    public function withVoiceId(string $voiceId): self
    {
        return new self(
            voiceId: $voiceId,
            stability: $this->stability,
            similarityBoost: $this->similarityBoost,
            style: $this->style,
            useSpeakerBoost: $this->useSpeakerBoost,
        );
    }

    public function withStability(float $stability): self
    {
        return new self(
            voiceId: $this->voiceId,
            stability: $stability,
            similarityBoost: $this->similarityBoost,
            style: $this->style,
            useSpeakerBoost: $this->useSpeakerBoost,
        );
    }

    public function toArray(): array
    {
        return [
            'voice_id' => $this->voiceId,
            'stability' => $this->stability,
            'similarity_boost' => $this->similarityBoost,
            'style' => $this->style,
            'use_speaker_boost' => $this->useSpeakerBoost,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            voiceId: $data['voice_id'] ?? null,
            stability: $data['stability'] ?? 0.5,
            similarityBoost: $data['similarity_boost'] ?? 0.75,
            style: $data['style'] ?? 0.0,
            useSpeakerBoost: $data['use_speaker_boost'] ?? true,
        );
    }

    private function validateRange(string $property, float $value, float $min, float $max): void
    {
        if ($value < $min || $value > $max) {
            throw new \InvalidArgumentException(
                "{$property} must be between {$min} and {$max}, got {$value}"
            );
        }
    }
}
