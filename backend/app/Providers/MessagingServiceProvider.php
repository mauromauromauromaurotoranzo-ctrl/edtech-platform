<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\Services\NotificationService;
use App\Domain\RepositoryInterfaces\ConversationRepositoryInterface;
use App\Domain\RepositoryInterfaces\CourseRepositoryInterface;
use App\Domain\RepositoryInterfaces\InstructorRepositoryInterface;
use App\Domain\RepositoryInterfaces\KnowledgeBaseRepositoryInterface;
use App\Domain\RepositoryInterfaces\MessageSenderInterface;
use App\Domain\RepositoryInterfaces\NotificationRepositoryInterface;
use App\Domain\RepositoryInterfaces\StudentChannelPreferenceRepositoryInterface;
use App\Domain\RepositoryInterfaces\StudentRepositoryInterface;
use App\Domain\RepositoryInterfaces\SubscriptionRepositoryInterface;
use App\Domain\RepositoryInterfaces\UserRepositoryInterface;
use App\Infrastructure\ExternalServices\Email\EmailSender;
use App\Infrastructure\ExternalServices\Telegram\TelegramSender;
use App\Infrastructure\ExternalServices\WhatsApp\WhatsAppSender;
use App\Infrastructure\Persistence\Eloquent\ConversationModel;
use App\Infrastructure\Persistence\Eloquent\ConversationRepository;
use App\Infrastructure\Persistence\Eloquent\CourseModel;
use App\Infrastructure\Persistence\Eloquent\CourseRepository;
use App\Infrastructure\Persistence\Eloquent\InstructorModel;
use App\Infrastructure\Persistence\Eloquent\InstructorRepository;
use App\Infrastructure\Persistence\Eloquent\KnowledgeBaseModel;
use App\Infrastructure\Persistence\Eloquent\KnowledgeBaseRepository;
use App\Infrastructure\Persistence\Eloquent\NotificationModel;
use App\Infrastructure\Persistence\Eloquent\NotificationRepository;
use App\Infrastructure\Persistence\Eloquent\StudentChannelPreferenceModel;
use App\Infrastructure\Persistence\Eloquent\StudentChannelPreferenceRepository;
use App\Infrastructure\Persistence\Eloquent\StudentModel;
use App\Infrastructure\Persistence\Eloquent\StudentRepository;
use App\Infrastructure\Persistence\Eloquent\SubscriptionModel;
use App\Infrastructure\Persistence\Eloquent\SubscriptionRepository;
use App\Infrastructure\Persistence\Eloquent\UserModel;
use App\Infrastructure\Persistence\Eloquent\UserRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class MessagingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repository bindings
        $this->app->bind(UserRepositoryInterface::class, function ($app) {
            return new UserRepository(new UserModel());
        });

        $this->app->bind(InstructorRepositoryInterface::class, function ($app) {
            return new InstructorRepository(new InstructorModel());
        });

        $this->app->bind(StudentRepositoryInterface::class, function ($app) {
            return new StudentRepository(new StudentModel());
        });

        $this->app->bind(KnowledgeBaseRepositoryInterface::class, function ($app) {
            return new KnowledgeBaseRepository(new KnowledgeBaseModel());
        });

        $this->app->bind(CourseRepositoryInterface::class, function ($app) {
            return new CourseRepository(new CourseModel());
        });

        $this->app->bind(SubscriptionRepositoryInterface::class, function ($app) {
            return new SubscriptionRepository(new SubscriptionModel());
        });

        $this->app->bind(ConversationRepositoryInterface::class, function ($app) {
            return new ConversationRepository(new ConversationModel());
        });

        $this->app->bind(NotificationRepositoryInterface::class, function ($app) {
            return new NotificationRepository(new NotificationModel());
        });

        $this->app->bind(StudentChannelPreferenceRepositoryInterface::class, function ($app) {
            return new StudentChannelPreferenceRepository(new StudentChannelPreferenceModel());
        });

        // Message Senders
        $this->app->bind(TelegramSender::class, function ($app) {
            $token = config('services.telegram.bot_token');
            if (!$token) {
                throw new \RuntimeException('Telegram bot token not configured');
            }
            return new TelegramSender($token);
        });

        $this->app->bind(WhatsAppSender::class, function ($app) {
            return new WhatsAppSender(
                apiUrl: config('services.whatsapp.api_url', 'https://graph.facebook.com/v18.0'),
                accessToken: config('services.whatsapp.access_token', ''),
                phoneNumberId: config('services.whatsapp.phone_number_id', ''),
            );
        });

        $this->app->bind(EmailSender::class, function ($app) {
            return new EmailSender(
                fromAddress: config('mail.from.address', 'noreply@edtech.local'),
                fromName: config('mail.from.name', 'EdTech Platform'),
            );
        });

        // Notification Service with all senders registered
        $this->app->singleton(NotificationService::class, function ($app) {
            $service = new NotificationService(
                notificationRepository: $app->make(NotificationRepositoryInterface::class),
                preferenceRepository: $app->make(StudentChannelPreferenceRepositoryInterface::class),
                logger: Log::channel(),
            );

            // Register all available senders
            try {
                $service->registerSender($app->make(TelegramSender::class));
            } catch (\Throwable $e) {
                Log::warning('Telegram sender not registered: ' . $e->getMessage());
            }

            try {
                $service->registerSender($app->make(WhatsAppSender::class));
            } catch (\Throwable $e) {
                Log::warning('WhatsApp sender not registered: ' . $e->getMessage());
            }

            try {
                $service->registerSender($app->make(EmailSender::class));
            } catch (\Throwable $e) {
                Log::warning('Email sender not registered: ' . $e->getMessage());
            }

            return $service;
        });
    }

    public function boot(): void
    {
        //
    }
}
