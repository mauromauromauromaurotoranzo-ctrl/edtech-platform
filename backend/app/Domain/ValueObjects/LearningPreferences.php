<?php

namespace App\Domain\ValueObjects;

class LearningPreferences
{
    private string $learningStyle;
    private string $difficultyPreference;
    private array $preferredChannels;
    private bool $dailyChallengesEnabled;
    private ?string $reminderTime;
    private int $sessionDurationMinutes;

    public function __construct(
        string $learningStyle = 'visual',
        string $difficultyPreference = 'adaptive',
        array $preferredChannels = ['app'],
        bool $dailyChallengesEnabled = true,
        ?string $reminderTime = '09:00',
        int $sessionDurationMinutes = 30
    ) {
        $this->learningStyle = $learningStyle;
        $this->difficultyPreference = $difficultyPreference;
        $this->preferredChannels = $preferredChannels;
        $this->dailyChallengesEnabled = $dailyChallengesEnabled;
        $this->reminderTime = $reminderTime;
        $this->sessionDurationMinutes = $sessionDurationMinutes;
    }

    public function getLearningStyle(): string
    {
        return $this->learningStyle;
    }

    public function getDifficultyPreference(): string
    {
        return $this->difficultyPreference;
    }

    public function getPreferredChannels(): array
    {
        return $this->preferredChannels;
    }

    public function isDailyChallengesEnabled(): bool
    {
        return $this->dailyChallengesEnabled;
    }

    public function getReminderTime(): ?string
    {
        return $this->reminderTime;
    }

    public function getSessionDurationMinutes(): int
    {
        return $this->sessionDurationMinutes;
    }

    public function toArray(): array
    {
        return [
            'learning_style' => $this->learningStyle,
            'difficulty_preference' => $this->difficultyPreference,
            'preferred_channels' => $this->preferredChannels,
            'daily_challenges_enabled' => $this->dailyChallengesEnabled,
            'reminder_time' => $this->reminderTime,
            'session_duration_minutes' => $this->sessionDurationMinutes,
        ];
    }
}
