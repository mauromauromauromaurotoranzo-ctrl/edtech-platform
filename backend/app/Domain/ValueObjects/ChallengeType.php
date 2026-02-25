<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

enum ChallengeType: string
{
    case QUIZ = 'quiz';
    case PUZZLE = 'puzzle';
    case SCENARIO = 'scenario';
    case FLASHCARD = 'flashcard';
    case CODE = 'code';
    case MATCHING = 'matching';

    public function getLabel(): string
    {
        return match($this) {
            self::QUIZ => 'Pregunta de opción múltiple',
            self::PUZZLE => 'Rompecabezas lógico',
            self::SCENARIO => 'Escenario práctico',
            self::FLASHCARD => 'Tarjeta de memoria',
            self::CODE => 'Ejercicio de código',
            self::MATCHING => 'Emparejamiento',
        };
    }

    public function getDescription(): string
    {
        return match($this) {
            self::QUIZ => 'Responde una pregunta seleccionando la opción correcta',
            self::PUZZLE => 'Resuelve un problema lógico o matemático',
            self::SCENARIO => 'Analiza un caso práctico y responde',
            self::FLASHCARD => 'Memoriza y recuerda información clave',
            self::CODE => 'Completa o corrige código',
            self::MATCHING => 'Relaciona conceptos con sus definiciones',
        };
    }

    public function requiresOptions(): bool
    {
        return in_array($this, [self::QUIZ, self::MATCHING]);
    }

    public function isAutoGradable(): bool
    {
        return in_array($this, [self::QUIZ, self::PUZZLE, self::MATCHING, self::CODE]);
    }
}
