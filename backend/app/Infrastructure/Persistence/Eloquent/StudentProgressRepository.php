<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Entities\StudentProgress;
use App\Domain\RepositoryInterfaces\StudentProgressRepositoryInterface;

class StudentProgressRepository implements StudentProgressRepositoryInterface
{
    public function __construct(
        private StudentProgressModel $model
    ) {}

    public function findById(int $id): ?StudentProgress
    {
        $record = $this->model->find($id);
        return $record ? $this->toEntity($record) : null;
    }

    public function findByStudentId(int $studentId): array
    {
        return $this->model
            ->where('student_id', $studentId)
            ->with('knowledgeBase')
            ->get()
            ->map(fn($r) => $this->toEntity($r))
            ->toArray();
    }

    public function findByStudentAndKnowledgeBase(int $studentId, int $knowledgeBaseId): ?StudentProgress
    {
        $record = $this->model
            ->where('student_id', $studentId)
            ->where('knowledge_base_id', $knowledgeBaseId)
            ->first();

        return $record ? $this->toEntity($record) : null;
    }

    public function findTopStudents(int $knowledgeBaseId, int $limit = 10): array
    {
        return $this->model
            ->where('knowledge_base_id', $knowledgeBaseId)
            ->orderBy('total_points', 'desc')
            ->limit($limit)
            ->with('student')
            ->get()
            ->map(fn($r) => $this->toEntity($r))
            ->toArray();
    }

    public function save(StudentProgress $progress): void
    {
        $data = [
            'student_id' => $progress->getStudentId(),
            'knowledge_base_id' => $progress->getKnowledgeBaseId(),
            'current_streak' => $progress->getCurrentStreak(),
            'longest_streak' => $progress->getLongestStreak(),
            'total_points' => $progress->getTotalPoints(),
            'challenges_completed' => $progress->getChallengesCompleted(),
            'challenges_correct' => $progress->getChallengesCorrect(),
            'last_challenge_date' => $progress->getLastChallengeDate(),
            'achievements' => json_encode($progress->getAchievements()),
            'metadata' => json_encode([]),
        ];

        if ($progress->getId()) {
            $this->model->where('id', $progress->getId())->update($data);
        } else {
            $record = $this->model->create($data);
            // Note: In real implementation, we'd need to update the entity with the new ID
        }
    }

    public function delete(int $id): void
    {
        $this->model->destroy($id);
    }

    public function deleteByStudentId(int $studentId): void
    {
        $this->model->where('student_id', $studentId)->delete();
    }

    public function getLeaderboard(int $knowledgeBaseId, int $limit = 20): array
    {
        $records = $this->model
            ->where('knowledge_base_id', $knowledgeBaseId)
            ->orderBy('total_points', 'desc')
            ->orderBy('challenges_completed', 'desc')
            ->limit($limit)
            ->with('student')
            ->get();

        $leaderboard = [];
        $rank = 1;

        foreach ($records as $record) {
            $progress = $this->toEntity($record);
            
            $leaderboard[] = [
                'rank' => $rank++,
                'student_id' => $progress->getStudentId(),
                'student_name' => $record->student?->name ?? 'Anonymous',
                'total_points' => $progress->getTotalPoints(),
                'level' => $progress->getLevel(),
                'current_streak' => $progress->getCurrentStreak(),
                'accuracy' => $progress->getAccuracy(),
                'achievements_count' => count($progress->getAchievements()),
            ];
        }

        return $leaderboard;
    }

    private function toEntity(StudentProgressModel $model): StudentProgress
    {
        return StudentProgress::fromDatabase([
            'id' => $model->id,
            'student_id' => $model->student_id,
            'knowledge_base_id' => $model->knowledge_base_id,
            'current_streak' => $model->current_streak,
            'longest_streak' => $model->longest_streak,
            'total_points' => $model->total_points,
            'challenges_completed' => $model->challenges_completed,
            'challenges_correct' => $model->challenges_correct,
            'last_challenge_date' => $model->last_challenge_date?->toDateTimeString(),
            'achievements' => $model->achievements,
            'metadata' => $model->metadata,
            'created_at' => $model->created_at->toDateTimeString(),
            'updated_at' => $model->updated_at->toDateTimeString(),
        ]);
    }
}
