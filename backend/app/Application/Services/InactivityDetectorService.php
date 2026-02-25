<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\RepositoryInterfaces\ConversationRepositoryInterface;
use App\Domain\RepositoryInterfaces\StudentRepositoryInterface;
use App\Domain\ValueObjects\ReminderType;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;

class InactivityDetectorService
{
    public function __construct(
        private StudentRepositoryInterface $studentRepository,
        private ConversationRepositoryInterface $conversationRepository,
        private SmartReminderService $reminderService,
        private LoggerInterface $logger,
    ) {}

    /**
     * Detect inactive students and create reminders
     */
    public function detectAndNotify(int $inactivityDays = 3): array
    {
        $students = $this->studentRepository->findAll();
        $notified = [];

        foreach ($students as $student) {
            // Get last conversation
            $conversations = $this->conversationRepository->findByStudentId($student->getId());
            
            if (empty($conversations)) {
                // Never started - skip or send welcome reminder
                continue;
            }

            $lastConversation = $conversations[0]; // Most recent
            $lastActivity = $lastConversation->getLastMessageAt() ?? $lastConversation->getCreatedAt();
            
            $daysSinceActivity = (new DateTimeImmutable())->diff($lastActivity)->days;

            if ($daysSinceActivity >= $inactivityDays) {
                // Create inactivity reminder
                $this->createInactivityReminder($student->getId(), $daysSinceActivity);
                $notified[] = [
                    'student_id' => $student->getId(),
                    'days_inactive' => $daysSinceActivity,
                ];
            }
        }

        $this->logger->info('Inactivity detection completed', [
            'checked' => count($students),
            'notified' => count($notified),
        ]);

        return $notified;
    }

    private function createInactivityReminder(int $studentId, int $daysInactive): void
    {
        $messages = [
            3 => "👋 ¡Te extrañamos! Hace 3 días que no practicas. ¿Todo bien?",
            7 => "🔥 Tu racha se está enfriando. ¡Vamos a calentar motores!",
            14 => "📚 ¿Necesitas ayuda? Hace 2 semanas que no estudias. Estoy aquí para apoyarte.",
            30 => "🚀 No abandones tu aprendizaje. ¡Cada pequeño paso cuenta!",
        ];

        $message = $messages[$daysInactive] ?? "👋 ¿Listo para retomar donde lo dejaste?";

        $this->reminderService->createReminder(
            studentId: $studentId,
            knowledgeBaseId: 0, // General reminder
            type: ReminderType::INACTIVITY,
            title: 'Te extrañamos',
            message: $message,
            scheduledAt: new DateTimeImmutable(),
        );
    }
}
