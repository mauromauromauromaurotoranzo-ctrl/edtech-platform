<?php

declare(strict_types=1);

namespace App\Domain\Entities;

enum UserRole: string
{
    case STUDENT = 'student';
    case INSTRUCTOR = 'instructor';
    case ADMIN = 'admin';
}
