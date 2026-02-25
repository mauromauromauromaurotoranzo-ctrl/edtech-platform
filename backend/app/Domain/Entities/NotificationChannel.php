<?php

declare(strict_types=1);

namespace App\Domain\Entities;

enum NotificationChannel: string
{
    case WHATSAPP = 'whatsapp';
    case TELEGRAM = 'telegram';
    case EMAIL = 'email';
}
