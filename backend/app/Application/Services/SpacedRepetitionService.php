<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Entities\SpacedRepetitionItem;
use App\Domain\RepositoryInterfaces\ContentChunkRepositoryInterface;
use App\Domain\RepositoryInterfaces\SpacedRepetitionRepositoryInterface;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;

class SpacedRepetitionService
{
    public function __construct(
        private SpacedRepetitionRepositoryInterface $repository,
        private ContentChunkRepositoryInterface $chunkRepository,
        private NotificationService $notificationService,
        private LoggerInterface $logger,
    ) {}

    /**
     * Initialize spaced repetition for a student with content chunks
     */
    public function initializeForStudent(int $studentId, int $knowledgeBaseId): array
    {
        $chunks = $this->chunkRepository->findByKnowledgeBaseId($knowledgeBaseId);
        $created = [];

        foreach ($chunks as $chunk) {
            // Check if already exists
            $existing = $this->repository->findByStudentAndChunk($studentId, $chunk->getId());
            
            if (!$existing) {
                $item = SpacedRepetitionItem::create($studentId, $chunk->getId());
                $this->repository->save($item);
                $created[] = $item;
            }
        }

        $this->logger->info('Spaced repetition initialized', [
            'student_id' => $studentId,
            'items_created' => count($created),
        ]);

        return $created;
    }

    /**
     * Get due items for review
     */
    public function getDueItems(int $studentId): array
    {
        return $this->repository->findDueItems($studentId);
    }

    /**
     * Review an item
     */
    public function reviewItem(int $itemId, int $quality): SpacedRepetitionItem
    {
        $item = $this->repository->findById($itemId);
        
        if (!$item) {
            throw new \InvalidArgumentException('Item not found');
        }

        $item->review($quality);
        $this->repository->save($item);

        $this->logger->info('Item reviewed', [
            'item_id' => $itemId,
            'quality' => $quality,
            'next_review' => $item->getNextReviewAt()?->format('Y-m-d'),
        ]);

        return $item;
    }

    /**
     * Send reminders for due items
     */
    public function sendReminders(): array
    {
        $dueItems = $this->repository->findAllDue();
        $sent = 0;

        foreach ($dueItems as $item) {
            try {
                $chunk = $this->chunkRepository->findById($item->getContentChunkId());
                
                if (!$chunk) {
                    continue;
                }

                $message = "🧠 Repaso espaciado\n\n";
                $message .= "Es hora de revisar este concepto:\n";
                $message .= substr($chunk->getContent(), 0, 200) . "...\n\n";
                $message .= "¿Qué tan bien lo recuerdas? (0-5)";

                $this->notificationService->sendToStudent(
                    studentId: $item->getStudentId(),
                    subject: '🧠 Momento de Repasar',
                    content: $message,
                );

                $sent++;

            } catch (\Exception $e) {
                $this->logger->error('Failed to send SR reminder', [
                    'item_id' => $item->getId(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return ['sent' => $sent, 'total_due' => count($dueItems)];
    }

    /**
     * Get student stats
     */
    public function getStats(int $studentId): array
    {
        return $this->repository->getStats($studentId);
    }
}
