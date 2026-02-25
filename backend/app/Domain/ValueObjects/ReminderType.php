<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

enum ReminderType: string
{
    case SPACED_REPETITION = 'spaced_repetition';
    case INACTIVITY = 'inactivity';
    case EXAM_PREP = 'exam_prep';
    case STREAK_MAINTENANCE = 'streak_maintenance';
    case CUSTOM = 'custom';
    case CONTENT_SUGGESTION = 'content_suggestion';

    public function getLabel(): string
    {
        return match($this) {
            self::SPACED_REPETITION => 'Repetición Espaciada',
            self::INACTIVITY => 'Recordatorio de Inactividad',
            self::EXAM_PREP => 'Preparación de Examen',
            self::STREAK_MAINTENANCE => 'Mantener Racha',
            self::CUSTOM => 'Personalizado',
            self::CONTENT_SUGGESTION => 'Sugerencia de Contenido',
        };
    }

    public function getDefaultPriority(): float
    {
        return match($this) {
            self::EXAM_PREP => 2.0,
            self::STREAK_MAINTENANCE => 1.5,
            self::SPACED_REPETITION => 1.2,
            self::INACTIVITY => 1.0,
            self::CONTENT_SUGGESTION => 0.8,
            self::CUSTOM => 1.0,
        };
    }
}
