<?php

declare(strict_types=1);

namespace App\Domain\Services;

interface LLMServiceInterface
{
    /**
     * Generate a completion from the LLM
     * 
     * @param array<int, array{role: string, content: string}> $messages
     * @param array<string, mixed> $options
     * @return array{text: string, tokensUsed: int, finishReason: string}
     * @throws \RuntimeException If generation fails
     */
    public function chat(array $messages, array $options = []): array;
    
    /**
     * Generate a streaming completion
     * 
     * @param array<int, array{role: string, content: string}> $messages
     * @param callable(string $chunk): void $onChunk
     * @param array<string, mixed> $options
     */
    public function chatStream(array $messages, callable $onChunk, array $options = []): void;
    
    /**
     * Get available models
     * @return array<int, array{id: string, name: string, contextWindow: int}>
     */
    public function listModels(): array;
}
