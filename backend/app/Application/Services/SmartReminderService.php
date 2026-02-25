<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Entities\SmartReminder;
use App\Domain\RepositoryInterfaces\SmartReminderRepositoryInterface;
use App\Domain\ValueObjects\ReminderType;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;

class SmartReminderService
{
    public function __construct(
        private SmartReminderRepositoryInterface $repository,
        private NotificationService $notificationService,
        private LoggerInterface $logger,
    ) {}

    /**
     * Create a new reminder
     */
    public function createReminder(
        int $studentId,
        int $knowledgeBaseId,
        ReminderType $type,
        string $title,
        string $message,
        DateTimeImmutable $scheduledAt,
        bool $isRecurring = false,
        ?string $recurrencePattern = null,
        float $priority = 1.0,
    ): SmartReminder {
        $reminder = SmartReminder::create(
            studentId: $studentId,
            knowledgeBaseId: $knowledgeBaseId,
            type: $type,
            title: $title,
            message: $message,
            scheduledAt: $scheduledAt,
            isRecurring: $isRecurring,
            recurrencePattern: $recurrencePattern,
            priority: $priority,
        );

        $this->repository->save($reminder);

        $this->logger->info('Reminder created', [
            'student_id' => $studentId,
            'type' => $type->value,
        ]);

        return $reminder;
    }

    /**
     * Send due reminders
     */
    public function sendDueReminders(): array
    {
        $now = new DateTimeImmutable();
        $dueReminders = $this->repository->findDueReminders($now);
        
        $sent = 0;
        $failed = 0;

        foreach ($dueReminders as $reminder) {
            try {
                $this->notificationService->sendToStudent(
                    studentId: $reminder->getStudentId(),
                    subject: $reminder->getTitle(),
                    content: $reminder->getMessage(),
                );

                $reminder->markAsSent();
                $this->repository->save($reminder);
                
                $sent++;

            } catch (\Exception $e) {
                $this->logger->error('Failed to send reminder', [
                    'reminder_id' => $reminder->getId(),
                    'error' => $e->getMessage(),
                ]);
                $failed++;
            }
        }

        return ['sent' => $sent, 'failed' => $failed];
    }

    /**
     * Schedule exam reminder
     */
    public function scheduleExamReminder(int $studentId, int $knowledgeBaseId, DateTimeImmutable $examDate): SmartReminder
    {
        // Reminder 7 days before
        $reminder7Days = $this->createReminder(
            studentId: $studentId,
            knowledgeBaseId: $knowledgeBaseId,
            type: ReminderType::EXAM,
            title: '📚 Examen en 7 días',
            message: "Tu examen es el {$examDate->format('d/m/Y')}. ¡Es hora de repasar!",
            scheduledAt: $examDate->modify('-7 days'),
            priority: 2.0,
        );

        // Reminder 1 day before
        $this->createReminder(
            studentId: $studentId,
            knowledgeBaseId: $knowledgeBaseId,
            type: ReminderType::EXAM,
            title: '⚠️ Examen mañana',
            message: "¡Mañana es tu examen! Último repaso recomendado.",
            scheduledAt: $examDate->modify('-1 day'),
            priority: 3.0,
        );

        return $reminder7Days;
    }

    /**
     * Get active reminders for student
     */
    public function getActiveReminders(int $studentId): array
    {
        return $this->repository->findActiveByStudentId($studentId);
    }

    /**
     * Cancel reminder
     */
    public function cancelReminder(int $reminderId): void
    {
        $reminder = $this->repository->findById($reminderId);
        
        if ($reminder) {
            $reminder->deactivate();
            $this->repository->save($reminder);
        }
    }
}
