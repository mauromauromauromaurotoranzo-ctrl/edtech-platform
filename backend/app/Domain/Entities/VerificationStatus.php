<?php

declare(strict_types=1);

namespace App\Domain\Entities;

enum VerificationStatus: string
{
    case PENDING = 'pending';
    case VERIFIED = 'verified';
    case REJECTED = 'rejected';
}
