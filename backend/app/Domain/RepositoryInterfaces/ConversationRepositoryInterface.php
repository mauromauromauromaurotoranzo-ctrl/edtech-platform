<?php

declare(strict_types=1);

namespace App\Domain\RepositoryInterfaces;

use App\Domain\Entities\Conversation;

interface ConversationRepositoryInterface
{
    public function findById(int $id): ?Conversation;
    
    /**
     * @return Conversation[]
     */
    public function findAll(): array;
    
    /**
     * @return Conversation[]
     */
    public function findByStudentId(int $studentId): array;
    
    /**
     * @return Conversation[]
     */
    public function findByKnowledgeBaseId(int $knowledgeBaseId): array;
    
    public function findLatestByStudentAndKnowledgeBase(int $studentId, int $knowledgeBaseId): ?Conversation;
    
    public function save(Conversation $conversation): void;
    
    public function delete(int $id): void;
}
