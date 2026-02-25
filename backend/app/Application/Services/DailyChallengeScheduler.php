<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\RepositoryInterfaces\DailyChallengeRepositoryInterface;
use App\Domain\RepositoryInterfaces\StudentRepositoryInterface;
use App\Domain\RepositoryInterfaces\SubscriptionRepositoryInterface;
use Psr\Log\LoggerInterface;

class DailyChallengeScheduler
{
    public function __construct(
        private ChallengeGeneratorService $generatorService,
        private NotificationService $notificationService,
        private StudentRepositoryInterface $studentRepository,
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private DailyChallengeRepositoryInterface $challengeRepository,
        private LoggerInterface $logger,
    ) {}

    /**
     * Process daily challenges for all active students
     * This should be called by Laravel Scheduler once per day
     */
    public function processDailyChallenges(): array
    {
        $stats = [
            'processed' => 0,
            'generated' => 0,
            'sent' => 0,
            'failed' => 0,
        ];

        try {
            // Get all pending challenges for today
            $pendingChallenges = $this->challengeRepository->findPendingForToday();
            
            $this->logger->info('Processing daily challenges', [
                'pending_count' => count($pendingChallenges),
            ]);

            foreach ($pendingChallenges as $challenge) {
                $stats['processed']++;

                try {
                    // Send notification to student
                    $this->sendChallengeNotification($challenge);
                    
                    // Mark as sent
                    $challenge->markAsSent();
                    $this->challengeRepository->save($challenge);
                    
                    $stats['sent']++;

                } catch (\Exception $e) {
                    $this->logger->error('Failed to send challenge', [
                        'challenge_id' => $challenge->getId(),
                        'error' => $e->getMessage(),
                    ]);
                    $stats['failed']++;
                }
            }

            $this->logger->info('Daily challenges processing completed', $stats);

        } catch (\Exception $e) {
            $this->logger->error('Daily challenges processing failed', [
                'error' => $e->getMessage(),
            ]);
        }

        return $stats;
    }

    /**
     * Generate challenges for the next day
     * Should run at end of day to prepare next day's challenges
     */
    public function generateNextDayChallenges(): array
    {
        $stats = [
            'students_processed' => 0,
            'challenges_generated' => 0,
            'failed' => 0,
        ];

        try {
            // Get all active subscriptions
            $subscriptions = $this->subscriptionRepository->findActive();
            
            $this->logger->info('Generating next day challenges', [
                'active_subscriptions' => count($subscriptions),
            ]);

            // Group by student to avoid duplicates
            $studentKnowledgeBases = [];
            foreach ($subscriptions as $subscription) {
                $studentId = $subscription->getStudentId();
                $knowledgeBaseId = $subscription->getKnowledgeBaseId();
                
                if (!isset($studentKnowledgeBases[$studentId])) {
                    $studentKnowledgeBases[$studentId] = [];
                }
                $studentKnowledgeBases[$studentId][] = $knowledgeBaseId;
            }

            // Generate challenges for each student-knowledge base pair
            foreach ($studentKnowledgeBases as $studentId => $knowledgeBaseIds) {
                foreach ($knowledgeBaseIds as $knowledgeBaseId) {
                    try {
                        $challenge = $this->generatorService->generateChallenge(
                            studentId: $studentId,
                            knowledgeBaseId: $knowledgeBaseId,
                        );

                        if ($challenge) {
                            $stats['challenges_generated']++;
                        }

                    } catch (\Exception $e) {
                        $this->logger->error('Failed to generate challenge', [
                            'student_id' => $studentId,
                            'knowledge_base_id' => $knowledgeBaseId,
                            'error' => $e->getMessage(),
                        ]);
                        $stats['failed']++;
                    }
                }
                
                $stats['students_processed']++;
            }

            $this->logger->info('Next day challenges generation completed', $stats);

        } catch (\Exception $e) {
            $this->logger->error('Next day challenges generation failed', [
                'error' => $e->getMessage(),
            ]);
        }

        return $stats;
    }

    /**
     * Send challenge notification to student
     */
    private function sendChallengeNotification($challenge): void
    {
        $student = $this->studentRepository->findById($challenge->getStudentId());
        
        if (!$student) {
            throw new \RuntimeException('Student not found');
        }

        $typeLabel = $challenge->getType()->getLabel();
        
        $message = "🎯 ¡Tu Desafío Diario está listo!\n\n";
        $message .= "Tipo: {$typeLabel}\n";
        $message .= "Título: {$challenge->getTitle()}\n";
        $message .= "Puntos: {$challenge->getPoints()}\n\n";
        $message .= "Responde para mantener tu racha 🔥";

        $this->notificationService->sendToStudent(
            studentId: $challenge->getStudentId(),
            subject: '🎯 Desafío Diario',
            content: $message,
        );
    }

    /**
     * Get scheduler status summary
     */
    public function getStatus(): array
    {
        $pendingCount = count($this->challengeRepository->findPendingForToday());
        $totalChallenges = $this->challengeRepository->countByStudentId(0); // Would need a total count method
        
        return [
            'pending_for_today' => $pendingCount,
            'last_run' => null, // Would track in database
            'next_scheduled' => '08:00 AM', // Configurable
            'status' => $pendingCount > 0 ? 'ready' : 'completed',
        ];
    }
}
