<?php

declare(strict_types=1);

namespace App\Domain\Entities;

enum KnowledgeBaseStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';
}
