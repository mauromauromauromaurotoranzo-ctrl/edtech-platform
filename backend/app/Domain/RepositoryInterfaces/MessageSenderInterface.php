<?php

declare(strict_types=1);

namespace App\Domain\RepositoryInterfaces;

use App\Domain\Entities\Notification;

interface MessageSenderInterface
{
    /**
     * Send a notification through the specific channel
     * 
     * @return string The external message ID from the provider
     * @throws \RuntimeException If sending fails
     */
    public function send(Notification $notification, string $recipient): string;
    
    /**
     * Check if this sender supports the given notification type
     */
    public function supports(Notification $notification): bool;
    
    /**
     * Get the name of this sender channel
     */
    public function getChannelName(): string;
}
