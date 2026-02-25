<?php

declare(strict_types=1);

namespace App\Domain\Entities;

enum CourseStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';
}
