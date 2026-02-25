<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Entities\Conversation;
use App\Domain\Entities\Message;
use App\Domain\Entities\MessageRole;
use App\Domain\RepositoryInterfaces\ConversationRepositoryInterface;

class ConversationRepository implements ConversationRepositoryInterface
{
    public function __construct(
        private ConversationModel $model
    ) {}

    public function findById(int $id): ?Conversation
    {
        $record = $this->model->find($id);
        return $record ? $this->toEntity($record) : null;
    }

    public function findAll(): array
    {
        return $this->model->all()->map(fn($r) => $this->toEntity($r))->toArray();
    }

    public function findByStudentId(int $studentId): array
    {
        return $this->model->where('student_id', $studentId)
            ->get()
            ->map(fn($r) => $this->toEntity($r))
            ->toArray();
    }

    public function findByKnowledgeBaseId(int $knowledgeBaseId): array
    {
        return $this->model->where('knowledge_base_id', $knowledgeBaseId)
            ->get()
            ->map(fn($r) => $this->toEntity($r))
            ->toArray();
    }

    public function findLatestByStudentAndKnowledgeBase(int $studentId, int $knowledgeBaseId): ?Conversation
    {
        $record = $this->model
            ->where('student_id', $studentId)
            ->where('knowledge_base_id', $knowledgeBaseId)
            ->latest()
            ->first();
        
        return $record ? $this->toEntity($record) : null;
    }

    public function save(Conversation $conversation): void
    {
        $data = [
            'student_id' => $conversation->getStudentId(),
            'knowledge_base_id' => $conversation->getKnowledgeBaseId(),
            'messages' => array_map(fn(Message $m) => $m->toArray(), $conversation->getMessages()),
            'chunks_referenced' => $conversation->getChunksReferenced(),
            'engagement_score' => $conversation->getEngagementScore(),
        ];

        if ($conversation->getId()) {
            $this->model->where('id', $conversation->getId())->update($data);
        } else {
            $this->model->create($data);
        }
    }

    public function delete(int $id): void
    {
        $this->model->destroy($id);
    }

    private function toEntity(ConversationModel $model): Conversation
    {
        $messages = array_map(
            fn(array $m) => new Message(
                id: $m['id'],
                role: MessageRole::from($m['role']),
                content: $m['content'],
                tokensUsed: $m['tokens_used'] ?? null,
                createdAt: new \DateTimeImmutable($m['created_at']),
            ),
            $model->messages ?? []
        );

        return new Conversation(
            id: $model->id,
            studentId: $model->student_id,
            knowledgeBaseId: $model->knowledge_base_id,
            messages: $messages,
            chunksReferenced: $model->chunks_referenced ?? [],
            engagementScore: $model->engagement_score,
            createdAt: $model->created_at->toDateTimeImmutable(),
            updatedAt: $model->updated_at->toDateTimeImmutable(),
        );
    }
}
