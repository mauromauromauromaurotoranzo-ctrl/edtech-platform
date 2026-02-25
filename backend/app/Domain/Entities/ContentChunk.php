<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use DateTimeImmutable;

class ContentChunk
{
    public function __construct(
        private ?int $id,
        private int $knowledgeBaseId,
        private string $content,
        private ?array $embeddingVector,
        private string $sourceType,
        private ?int $pageNum,
        private ?string $section,
        private ?string $contextWindow,
        private array $metadata,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
    ) {}

    public static function create(
        int $knowledgeBaseId,
        string $content,
        string $sourceType = 'text',
        ?int $pageNum = null,
        ?string $section = null,
        ?string $contextWindow = null,
        array $metadata = [],
    ): self {
        $now = new DateTimeImmutable();
        return new self(
            id: null,
            knowledgeBaseId: $knowledgeBaseId,
            content: $content,
            embeddingVector: null,
            sourceType: $sourceType,
            pageNum: $pageNum,
            section: $section,
            contextWindow: $contextWindow,
            metadata: $metadata,
            createdAt: $now,
            updatedAt: $now,
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKnowledgeBaseId(): int
    {
        return $this->knowledgeBaseId;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getEmbeddingVector(): ?array
    {
        return $this->embeddingVector;
    }

    public function getSourceType(): string
    {
        return $this->sourceType;
    }

    public function getPageNum(): ?int
    {
        return $this->pageNum;
    }

    public function getSection(): ?string
    {
        return $this->section;
    }

    public function getContextWindow(): ?string
    {
        return $this->contextWindow;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setEmbeddingVector(array $vector): void
    {
        $this->embeddingVector = $vector;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function updateContent(string $content): void
    {
        $this->content = $content;
        $this->embeddingVector = null; // Invalidate embedding
        $this->updatedAt = new DateTimeImmutable();
    }

    public function hasEmbedding(): bool
    {
        return $this->embeddingVector !== null && !empty($this->embeddingVector);
    }
}
