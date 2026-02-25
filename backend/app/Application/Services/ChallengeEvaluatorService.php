<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Entities\DailyChallenge;
use App\Domain\Entities\StudentProgress;
use App\Domain\RepositoryInterfaces\DailyChallengeRepositoryInterface;
use App\Domain\RepositoryInterfaces\StudentProgressRepositoryInterface;
use App\Domain\ValueObjects\ChallengeType;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;

class ChallengeEvaluatorService
{
    public function __construct(
        private DailyChallengeRepositoryInterface $challengeRepository,
        private StudentProgressRepositoryInterface $progressRepository,
        private LoggerInterface $logger,
    ) {}

    /**
     * Submit and evaluate a challenge answer
     */
    public function submitAnswer(int $challengeId, string $answer): array
    {
        $challenge = $this->challengeRepository->findById($challengeId);
        
        if (!$challenge) {
            throw new \InvalidArgumentException('Challenge not found');
        }

        if ($challenge->isAnswered()) {
            throw new \RuntimeException('Challenge already answered');
        }

        // Submit answer
        $challenge->submitAnswer($answer);
        $this->challengeRepository->save($challenge);

        // Update student progress
        $this->updateProgress($challenge);

        $result = [
            'is_correct' => $challenge->isCorrect(),
            'points_earned' => $challenge->getPointsEarned(),
            'correct_answer' => $challenge->getCorrectAnswer(),
            'explanation' => $challenge->getExplanation(),
            'total_points' => null,
            'current_streak' => null,
            'new_achievements' => [],
        ];

        // Get updated progress
        $progress = $this->progressRepository->findByStudentAndKnowledgeBase(
            $challenge->getStudentId(),
            $challenge->getKnowledgeBaseId()
        );

        if ($progress) {
            $result['total_points'] = $progress->getTotalPoints();
            $result['current_streak'] = $progress->getCurrentStreak();
            $result['level'] = $progress->getLevel();
            $result['new_achievements'] = $this->getNewAchievements($progress);
        }

        $this->logger->info('Challenge answer submitted', [
            'challenge_id' => $challengeId,
            'student_id' => $challenge->getStudentId(),
            'is_correct' => $challenge->isCorrect(),
        ]);

        return $result;
    }

    /**
     * Evaluate free-text answers using AI (for non-auto-gradable types)
     */
    public function evaluateWithAI(DailyChallenge $challenge, string $answer, LLMServiceInterface $llmService): array
    {
        $prompt = <<<PROMPT
Evalúa la siguiente respuesta de un estudiante.

DESAFÍO: {$challenge->getTitle()}
CONTENIDO: {$challenge->getContent()}
RESPUESTA ESPERADA: {$challenge->getCorrectAnswer()}
EXPLICACIÓN: {$challenge->getExplanation()}

RESPUESTA DEL ESTUDIANTE:
{$answer}

Evalúa si la respuesta es correcta considerando:
1. Precisión del concepto principal
2. Completitud de la respuesta
3. Claridad en la explicación

Responde con JSON:
{
    "is_correct": true/false,
    "score": 0-100,
    "feedback": "Retroalimentación constructiva para el estudiante",
    "points_percent": 0-1 (porcentaje de puntos a otorgar)
}
PROMPT;

        try {
            $response = $llmService->chat([
                ['role' => 'system', 'content' => 'Eres un evaluador educativo justo y constructivo.'],
                ['role' => 'user', 'content' => $prompt],
            ], [
                'temperature' => 0.3,
            ]);

            $content = $response['text'];
            
            if (preg_match('/```json\s*(.*?)\s*```/s', $content, $matches)) {
                $content = $matches[1];
            }

            $evaluation = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Fallback to simple evaluation
                return [
                    'is_correct' => false,
                    'score' => 0,
                    'feedback' => 'No se pudo evaluar automáticamente. Un instructor revisará tu respuesta.',
                    'points_percent' => 0,
                ];
            }

            return $evaluation;

        } catch (\Exception $e) {
            $this->logger->error('AI evaluation failed', ['error' => $e->getMessage()]);
            
            return [
                'is_correct' => false,
                'score' => 0,
                'feedback' => 'Error en la evaluación. Intenta nuevamente.',
                'points_percent' => 0,
            ];
        }
    }

    /**
     * Skip a challenge (marks as answered with 0 points)
     */
    public function skipChallenge(int $challengeId): void
    {
        $challenge = $this->challengeRepository->findById($challengeId);
        
        if (!$challenge) {
            throw new \InvalidArgumentException('Challenge not found');
        }

        if ($challenge->isAnswered()) {
            throw new \RuntimeException('Challenge already answered');
        }

        $challenge->submitAnswer('SKIPPED');
        $this->challengeRepository->save($challenge);

        // Still update progress but with 0 points
        $this->updateProgress($challenge);

        $this->logger->info('Challenge skipped', [
            'challenge_id' => $challengeId,
            'student_id' => $challenge->getStudentId(),
        ]);
    }

    /**
     * Get student's daily challenge status
     */
    public function getDailyStatus(int $studentId, int $knowledgeBaseId): array
    {
        $today = new DateTimeImmutable();
        $challenge = $this->challengeRepository->findByStudentIdAndDate($studentId, $today);
        $progress = $this->progressRepository->findByStudentAndKnowledgeBase($studentId, $knowledgeBaseId);

        return [
            'has_challenge_today' => $challenge !== null,
            'challenge_id' => $challenge?->getId(),
            'is_answered' => $challenge?->isAnswered() ?? false,
            'type' => $challenge?->getType()->value,
            'title' => $challenge?->getTitle(),
            'current_streak' => $progress?->getCurrentStreak() ?? 0,
            'total_points' => $progress?->getTotalPoints() ?? 0,
            'level' => $progress?->getLevel() ?? 1,
        ];
    }

    /**
     * Get leaderboard for a knowledge base
     */
    public function getLeaderboard(int $knowledgeBaseId, int $limit = 20): array
    {
        return $this->progressRepository->getLeaderboard($knowledgeBaseId, $limit);
    }

    private function updateProgress(DailyChallenge $challenge): void
    {
        $progress = $this->progressRepository->findByStudentAndKnowledgeBase(
            $challenge->getStudentId(),
            $challenge->getKnowledgeBaseId()
        );

        if (!$progress) {
            $progress = StudentProgress::create(
                studentId: $challenge->getStudentId(),
                knowledgeBaseId: $challenge->getKnowledgeBaseId(),
            );
        }

        $progress->recordChallenge(
            isCorrect: $challenge->isCorrect() ?? false,
            pointsEarned: $challenge->getPointsEarned(),
            date: new DateTimeImmutable(),
        );

        $this->progressRepository->save($progress);
    }

    private function getNewAchievements(StudentProgress $progress): array
    {
        // This would track which achievements were newly earned
        // For now, return all achievements
        return $progress->getAchievements();
    }
}
