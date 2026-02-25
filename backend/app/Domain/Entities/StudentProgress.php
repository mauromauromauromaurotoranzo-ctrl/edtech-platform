<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use DateTimeImmutable;

class StudentProgress
{
    private int $currentStreak = 0;
    private int $longestStreak = 0;
    private int $totalPoints = 0;
    private int $challengesCompleted = 0;
    private int $challengesCorrect = 0;
    private ?DateTimeImmutable $lastChallengeDate = null;
    private array $achievements = [];
    private array $metadata = [];

    private function __construct(
        private ?int $id,
        private int $studentId,
        private int $knowledgeBaseId,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
    ) {}

    public static function create(
        int $studentId,
        int $knowledgeBaseId,
    ): self {
        $now = new DateTimeImmutable();
        
        return new self(
            id: null,
            studentId: $studentId,
            knowledgeBaseId: $knowledgeBaseId,
            createdAt: $now,
            updatedAt: $now,
        );
    }

    public static function fromDatabase(array $data): self
    {
        $progress = new self(
            id: $data['id'],
            studentId: $data['student_id'],
            knowledgeBaseId: $data['knowledge_base_id'],
            createdAt: new DateTimeImmutable($data['created_at']),
            updatedAt: new DateTimeImmutable($data['updated_at']),
        );

        $progress->currentStreak = $data['current_streak'] ?? 0;
        $progress->longestStreak = $data['longest_streak'] ?? 0;
        $progress->totalPoints = $data['total_points'] ?? 0;
        $progress->challengesCompleted = $data['challenges_completed'] ?? 0;
        $progress->challengesCorrect = $data['challenges_correct'] ?? 0;
        $progress->lastChallengeDate = $data['last_challenge_date'] 
            ? new DateTimeImmutable($data['last_challenge_date']) 
            : null;
        $progress->achievements = json_decode($data['achievements'] ?? '[]', true);
        $progress->metadata = json_decode($data['metadata'] ?? '[]', true);

        return $progress;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getStudentId(): int { return $this->studentId; }
    public function getKnowledgeBaseId(): int { return $this->knowledgeBaseId; }
    public function getCurrentStreak(): int { return $this->currentStreak; }
    public function getLongestStreak(): int { return $this->longestStreak; }
    public function getTotalPoints(): int { return $this->totalPoints; }
    public function getChallengesCompleted(): int { return $this->challengesCompleted; }
    public function getChallengesCorrect(): int { return $this->challengesCorrect; }
    public function getAccuracy(): float 
    { 
        return $this->challengesCompleted > 0 
            ? round(($this->challengesCorrect / $this->challengesCompleted) * 100, 2)
            : 0.0;
    }
    public function getLastChallengeDate(): ?DateTimeImmutable { return $this->lastChallengeDate; }
    public function getAchievements(): array { return $this->achievements; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): DateTimeImmutable { return $this->updatedAt; }

    // Business methods
    public function recordChallenge(bool $isCorrect, int $pointsEarned, DateTimeImmutable $date): void
    {
        $this->challengesCompleted++;
        $this->totalPoints += $pointsEarned;
        
        if ($isCorrect) {
            $this->challengesCorrect++;
        }

        // Update streak
        if ($this->lastChallengeDate) {
            $yesterday = (new DateTimeImmutable())->modify('-1 day')->setTime(0, 0, 0);
            $lastDate = $this->lastChallengeDate->setTime(0, 0, 0);
            
            if ($lastDate == $yesterday) {
                $this->currentStreak++;
            } elseif ($lastDate < $yesterday) {
                $this->currentStreak = 1;
            }
        } else {
            $this->currentStreak = 1;
        }

        if ($this->currentStreak > $this->longestStreak) {
            $this->longestStreak = $this->currentStreak;
        }

        $this->lastChallengeDate = $date;
        $this->updatedAt = new DateTimeImmutable();

        // Check achievements
        $this->checkAchievements();
    }

    public function addAchievement(string $achievementId, string $name, string $description): void
    {
        if (!isset($this->achievements[$achievementId])) {
            $this->achievements[$achievementId] = [
                'id' => $achievementId,
                'name' => $name,
                'description' => $description,
                'earned_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            ];
            $this->updatedAt = new DateTimeImmutable();
        }
    }

    public function hasAchievement(string $achievementId): bool
    {
        return isset($this->achievements[$achievementId]);
    }

    public function getLevel(): int
    {
        // Level based on total points (exponential growth)
        $level = 1;
        $pointsNeeded = 100;
        $remainingPoints = $this->totalPoints;

        while ($remainingPoints >= $pointsNeeded) {
            $remainingPoints -= $pointsNeeded;
            $pointsNeeded = (int) ($pointsNeeded * 1.5);
            $level++;
        }

        return $level;
    }

    public function getPointsToNextLevel(): int
    {
        $currentLevel = $this->getLevel();
        $pointsNeeded = 100;
        $totalPointsNeeded = 0;

        for ($i = 1; $i <= $currentLevel; $i++) {
            $totalPointsNeeded += $pointsNeeded;
            $pointsNeeded = (int) ($pointsNeeded * 1.5);
        }

        return $totalPointsNeeded - $this->totalPoints;
    }

    private function checkAchievements(): void
    {
        // First challenge
        if ($this->challengesCompleted === 1) {
            $this->addAchievement('first_challenge', 'Primer Desafío', 'Completaste tu primer desafío diario');
        }

        // Streak achievements
        if ($this->currentStreak === 7) {
            $this->addAchievement('streak_7', 'Semana Perfecta', '7 días consecutivos de desafíos');
        }
        if ($this->currentStreak === 30) {
            $this->addAchievement('streak_30', 'Mes de Fuego', '30 días consecutivos de desafíos');
        }

        // Points achievements
        if ($this->totalPoints >= 100 && !$this->hasAchievement('points_100')) {
            $this->addAchievement('points_100', 'Centenario', 'Acumulaste 100 puntos');
        }
        if ($this->totalPoints >= 500 && !$this->hasAchievement('points_500')) {
            $this->addAchievement('points_500', 'Experto', 'Acumulaste 500 puntos');
        }

        // Accuracy achievements
        if ($this->challengesCompleted >= 10 && $this->getAccuracy() >= 90) {
            $this->addAchievement('accuracy_90', 'Precisión Perfecta', '90% de precisión en 10+ desafíos');
        }
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->studentId,
            'knowledge_base_id' => $this->knowledgeBaseId,
            'current_streak' => $this->currentStreak,
            'longest_streak' => $this->longestStreak,
            'total_points' => $this->totalPoints,
            'challenges_completed' => $this->challengesCompleted,
            'challenges_correct' => $this->challengesCorrect,
            'accuracy' => $this->getAccuracy(),
            'level' => $this->getLevel(),
            'points_to_next_level' => $this->getPointsToNextLevel(),
            'last_challenge_date' => $this->lastChallengeDate?->format('Y-m-d H:i:s'),
            'achievements' => json_encode($this->achievements),
            'metadata' => json_encode($this->metadata),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
