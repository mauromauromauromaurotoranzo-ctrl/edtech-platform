<?php

declare(strict_types=1);

namespace App\Domain\Entities;

enum NotificationStatus: string
{
    case PENDING = 'pending';
    case SENT = 'sent';
    case DELIVERED = 'delivered';
    case READ = 'read';
    case FAILED = 'failed';
}
