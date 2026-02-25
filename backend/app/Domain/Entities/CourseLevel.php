<?php

declare(strict_types=1);

namespace App\Domain\Entities;

enum CourseLevel: string
{
    case BEGINNER = 'beginner';
    case INTERMEDIATE = 'intermediate';
    case ADVANCED = 'advanced';
    case EXPERT = 'expert';
}
