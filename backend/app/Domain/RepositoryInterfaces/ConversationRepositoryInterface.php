<?php

namespace App\Domain\RepositoryInterfaces;

use App\Domain\Entities\Conversation;

interface ConversationRepositoryInterface
{
    public function findById(string $id): ?Conversation;
    public function save(Conversation $conversation): void;
    public function delete(string $id): void;
    public function findByStudentId(string $studentId, int $page = 1, int $perPage = 20): array;
    public function findByKnowledgeBaseId(string $knowledgeBaseId, int $page = 1, int $perPage = 20): array;
    public function findByStudentAndKnowledgeBase(string $studentId, string $knowledgeBaseId): ?Conversation;
    public function getRecentConversations(int $limit = 10): array;
}
