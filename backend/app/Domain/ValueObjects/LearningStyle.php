<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

enum LearningStyle: string
{
    case VISUAL = 'visual';
    case AUDITORY = 'auditory';
    case READING = 'reading';
    case KINESTHETIC = 'kinesthetic';
    case BALANCED = 'balanced';
}
