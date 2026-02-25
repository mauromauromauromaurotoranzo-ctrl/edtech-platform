<?php

declare(strict_types=1);

namespace App\Domain\Entities;

enum SubscriptionStatus: string
{
    case ACTIVE = 'active';
    case CANCELLED = 'cancelled';
    case EXPIRED = 'expired';
}
