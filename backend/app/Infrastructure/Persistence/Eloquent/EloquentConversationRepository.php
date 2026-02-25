<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Entities\Conversation;
use App\Domain\RepositoryInterfaces\ConversationRepositoryInterface;

class EloquentConversationRepository implements ConversationRepositoryInterface
{
    public function findById(string $id): ?Conversation
    {
        $model = ConversationModel::find($id);
        return $model ? $this->toEntity($model) : null;
    }

    public function save(Conversation $conversation): void
    {
        ConversationModel::updateOrCreate(
            ['id' => $conversation->getId()],
            [
                'student_id' => $conversation->getStudentId(),
                'knowledge_base_id' => $conversation->getKnowledgeBaseId(),
                'messages' => $conversation->getMessages(),
                'context_retrieval' => $conversation->getContextRetrieval(),
                'engagement_score' => $conversation->getEngagementScore(),
                'last_message_at' => $conversation->getLastMessageAt(),
            ]
        );
    }

    public function delete(string $id): void
    {
        ConversationModel::destroy($id);
    }

    public function findByStudentId(string $studentId, int $page = 1, int $perPage = 20): array
    {
        $models = ConversationModel::where('student_id', $studentId)
            ->orderBy('last_message_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return $this->paginateToArray($models);
    }

    public function findByKnowledgeBaseId(string $knowledgeBaseId, int $page = 1, int $perPage = 20): array
    {
        $models = ConversationModel::where('knowledge_base_id', $knowledgeBaseId)
            ->orderBy('last_message_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return $this->paginateToArray($models);
    }

    public function findByStudentAndKnowledgeBase(string $studentId, string $knowledgeBaseId): ?Conversation
    {
        $model = ConversationModel::where('student_id', $studentId)
            ->where('knowledge_base_id', $knowledgeBaseId)
            ->first();

        return $model ? $this->toEntity($model) : null;
    }

    public function getRecentConversations(int $limit = 10): array
    {
        $models = ConversationModel::recent($limit)->get();

        return $models->map(fn ($model) => $this->toEntity($model))->all();
    }

    private function toEntity(ConversationModel $model): Conversation
    {
        $conversation = new Conversation(
            $model->id,
            $model->student_id,
            $model->knowledge_base_id,
            $model->created_at
        );

        // Restore messages
        foreach ($model->messages as $message) {
            $conversation->addMessage(
                $message['role'],
                $message['content'],
                $message['tokens_used'] ?? null
            );
        }

        if ($model->context_retrieval) {
            $conversation->setContextRetrieval($model->context_retrieval);
        }

        if ($model->engagement_score) {
            $conversation->setEngagementScore($model->engagement_score);
        }

        return $conversation;
    }

    private function paginateToArray($models): array
    {
        return [
            'data' => $models->map(fn ($model) => $this->toEntity($model))->all(),
            'pagination' => [
                'current_page' => $models->currentPage(),
                'total_pages' => $models->lastPage(),
                'total_items' => $models->total(),
            ],
        ];
    }
}
