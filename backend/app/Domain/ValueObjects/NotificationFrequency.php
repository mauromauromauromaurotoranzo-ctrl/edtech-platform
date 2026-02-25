<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

enum NotificationFrequency: string
{
    case IMMEDIATE = 'immediate';
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case NEVER = 'never';
}
