<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

final class LearningPreferences
{
    public function __construct(
        private LearningStyle $learningStyle = LearningStyle::BALANCED,
        private NotificationFrequency $notificationFrequency = NotificationFrequency::DAILY,
        private bool $voiceEnabled = false,
        private ?VoiceSettings $voiceSettings = null,
        private string $preferredLanguage = 'es',
        private int $dailyGoalMinutes = 30,
    ) {}

    public function getLearningStyle(): LearningStyle
    {
        return $this->learningStyle;
    }

    public function getNotificationFrequency(): NotificationFrequency
    {
        return $this->notificationFrequency;
    }

    public function isVoiceEnabled(): bool
    {
        return $this->voiceEnabled;
    }

    public function getVoiceSettings(): ?VoiceSettings
    {
        return $this->voiceSettings;
    }

    public function getPreferredLanguage(): string
    {
        return $this->preferredLanguage;
    }

    public function getDailyGoalMinutes(): int
    {
        return $this->dailyGoalMinutes;
    }

    public function withLearningStyle(LearningStyle $style): self
    {
        return new self(
            learningStyle: $style,
            notificationFrequency: $this->notificationFrequency,
            voiceEnabled: $this->voiceEnabled,
            voiceSettings: $this->voiceSettings,
            preferredLanguage: $this->preferredLanguage,
            dailyGoalMinutes: $this->dailyGoalMinutes,
        );
    }

    public function withNotificationFrequency(NotificationFrequency $frequency): self
    {
        return new self(
            learningStyle: $this->learningStyle,
            notificationFrequency: $frequency,
            voiceEnabled: $this->voiceEnabled,
            voiceSettings: $this->voiceSettings,
            preferredLanguage: $this->preferredLanguage,
            dailyGoalMinutes: $this->dailyGoalMinutes,
        );
    }

    public function withVoiceEnabled(bool $enabled, ?VoiceSettings $settings = null): self
    {
        return new self(
            learningStyle: $this->learningStyle,
            notificationFrequency: $this->notificationFrequency,
            voiceEnabled: $enabled,
            voiceSettings: $settings,
            preferredLanguage: $this->preferredLanguage,
            dailyGoalMinutes: $this->dailyGoalMinutes,
        );
    }

    public function withDailyGoal(int $minutes): self
    {
        return new self(
            learningStyle: $this->learningStyle,
            notificationFrequency: $this->notificationFrequency,
            voiceEnabled: $this->voiceEnabled,
            voiceSettings: $this->voiceSettings,
            preferredLanguage: $this->preferredLanguage,
            dailyGoalMinutes: max(5, min(180, $minutes)),
        );
    }

    public function toArray(): array
    {
        return [
            'learning_style' => $this->learningStyle->value,
            'notification_frequency' => $this->notificationFrequency->value,
            'voice_enabled' => $this->voiceEnabled,
            'voice_settings' => $this->voiceSettings?->toArray(),
            'preferred_language' => $this->preferredLanguage,
            'daily_goal_minutes' => $this->dailyGoalMinutes,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            learningStyle: LearningStyle::tryFrom($data['learning_style'] ?? '') ?? LearningStyle::BALANCED,
            notificationFrequency: NotificationFrequency::tryFrom($data['notification_frequency'] ?? '') ?? NotificationFrequency::DAILY,
            voiceEnabled: $data['voice_enabled'] ?? false,
            voiceSettings: isset($data['voice_settings']) ? VoiceSettings::fromArray($data['voice_settings']) : null,
            preferredLanguage: $data['preferred_language'] ?? 'es',
            dailyGoalMinutes: $data['daily_goal_minutes'] ?? 30,
        );
    }
}
