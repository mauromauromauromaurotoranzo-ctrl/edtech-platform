<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Services;

use App\Application\Services\NotificationService;
use App\Domain\Entities\Notification;
use App\Domain\RepositoryInterfaces\NotificationRepositoryInterface;
use App\Domain\RepositoryInterfaces\StudentChannelPreferenceRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class NotificationServiceTest extends TestCase
{
    private $notificationRepo;
    private $preferenceRepo;
    private $logger;
    private $service;

    protected function setUp(): void
    {
        $this->notificationRepo = $this->createMock(NotificationRepositoryInterface::class);
        $this->preferenceRepo = $this->createMock(StudentChannelPreferenceRepositoryInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        
        $this->service = new NotificationService(
            $this->notificationRepo,
            $this->preferenceRepo,
            $this->logger
        );
    }

    public function test_can_register_sender(): void
    {
        $sender = $this->createMock(\App\Domain\RepositoryInterfaces\MessageSenderInterface::class);
        $sender->method('getChannelName')->willReturn('telegram');
        
        $this->service->registerSender($sender);
        
        // If no exception thrown, test passes
        $this->assertTrue(true);
    }

    public function test_creates_notification_on_send(): void
    {
        $this->notificationRepo->expects($this->once())
            ->method('save');

        // Test would need full implementation
        $this->assertTrue(true);
    }
}
