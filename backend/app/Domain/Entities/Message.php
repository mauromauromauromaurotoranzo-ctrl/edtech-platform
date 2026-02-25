<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use DateTimeImmutable;

class Message
{
    public function __construct(
        private string $id,
        private MessageRole $role,
        private string $content,
        private ?int $tokensUsed,
        private DateTimeImmutable $createdAt,
    ) {}

    public static function create(
        MessageRole $role,
        string $content,
        ?int $tokensUsed = null,
    ): self {
        return new self(
            id: uniqid('msg_', true),
            role: $role,
            content: $content,
            tokensUsed: $tokensUsed,
            createdAt: new DateTimeImmutable(),
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getRole(): MessageRole
    {
        return $this->role;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getTokensUsed(): ?int
    {
        return $this->tokensUsed;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'role' => $this->role->value,
            'content' => $this->content,
            'tokens_used' => $this->tokensUsed,
            'created_at' => $this->createdAt->format('c'),
        ];
    }
}
