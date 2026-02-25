<?php

declare(strict_types=1);

namespace App\Infrastructure\ExternalServices\OpenRouter;

use App\Domain\Services\LLMServiceInterface;
use Illuminate\Support\Facades\Http;

class OpenRouterService implements LLMServiceInterface
{
    private string $apiKey;
    private string $baseUrl = 'https://openrouter.ai/api/v1';
    private string $defaultModel;

    public function __construct(string $apiKey, string $defaultModel = 'anthropic/claude-3.5-sonnet')
    {
        $this->apiKey = $apiKey;
        $this->defaultModel = $defaultModel;
    }

    public function chat(array $messages, array $options = []): array
    {
        $model = $options['model'] ?? $this->defaultModel;
        $temperature = $options['temperature'] ?? 0.7;
        $maxTokens = $options['max_tokens'] ?? 4096;

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
            'HTTP-Referer' => $options['site_url'] ?? 'https://edtech.local',
            'X-Title' => $options['site_name'] ?? 'EdTech Platform',
        ])->post("{$this->baseUrl}/chat/completions", [
            'model' => $model,
            'messages' => $messages,
            'temperature' => $temperature,
            'max_tokens' => $maxTokens,
            'stream' => false,
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException(
                "OpenRouter API error: {$response->body()}"
            );
        }

        $data = $response->json();

        return [
            'text' => $data['choices'][0]['message']['content'] ?? '',
            'tokensUsed' => $data['usage']['total_tokens'] ?? 0,
            'finishReason' => $data['choices'][0]['finish_reason'] ?? 'unknown',
        ];
    }

    public function chatStream(array $messages, callable $onChunk, array $options = []): void
    {
        $model = $options['model'] ?? $this->defaultModel;
        $temperature = $options['temperature'] ?? 0.7;
        $maxTokens = $options['max_tokens'] ?? 4096;

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
            'HTTP-Referer' => $options['site_url'] ?? 'https://edtech.local',
            'X-Title' => $options['site_name'] ?? 'EdTech Platform',
            'Accept' => 'text/event-stream',
        ])->post("{$this->baseUrl}/chat/completions", [
            'model' => $model,
            'messages' => $messages,
            'temperature' => $temperature,
            'max_tokens' => $maxTokens,
            'stream' => true,
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException(
                "OpenRouter API error: {$response->body()}"
            );
        }

        // Process SSE stream
        $body = $response->body();
        $lines = explode("\n", $body);
        
        foreach ($lines as $line) {
            if (str_starts_with($line, 'data: ')) {
                $data = json_decode(substr($line, 6), true);
                if ($data && isset($data['choices'][0]['delta']['content'])) {
                    $onChunk($data['choices'][0]['delta']['content']);
                }
            }
        }
    }

    public function listModels(): array
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
        ])->get("{$this->baseUrl}/models");

        if (!$response->successful()) {
            throw new \RuntimeException(
                "OpenRouter API error: {$response->body()}"
            );
        }

        $data = $response->json();
        $models = [];

        foreach ($data['data'] ?? [] as $model) {
            $models[] = [
                'id' => $model['id'],
                'name' => $model['name'] ?? $model['id'],
                'contextWindow' => $model['context_length'] ?? 4096,
            ];
        }

        return $models;
    }

    /**
     * Generate RAG response with context
     */
    public function generateRagResponse(
        string $query,
        array $contextChunks,
        string $systemPrompt = null,
        array $conversationHistory = []
    ): array {
        $contextText = implode("\n\n", array_map(
            fn($chunk) => $chunk['content'],
            $contextChunks
        ));

        $defaultSystemPrompt = "Eres un tutor experto. Responde basándote ÚNICAMENTE en el contexto proporcionado. " .
            "Si la respuesta no está en el contexto, indica que no tienes esa información. " .
            "Sé claro, conciso y didáctico.";

        $messages = [
            [
                'role' => 'system',
                'content' => ($systemPrompt ?? $defaultSystemPrompt) . "\n\nContexto:\n{$contextText}"
            ],
        ];

        // Add conversation history
        foreach ($conversationHistory as $msg) {
            $messages[] = $msg;
        }

        // Add current query
        $messages[] = [
            'role' => 'user',
            'content' => $query
        ];

        return $this->chat($messages, [
            'temperature' => 0.5, // Lower temperature for more factual responses
        ]);
    }
}
