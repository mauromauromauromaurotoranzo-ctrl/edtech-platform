<?php

declare(strict_types=1);

namespace App\Infrastructure\ExternalServices\OpenAI;

use App\Domain\Services\EmbeddingServiceInterface;
use Illuminate\Support\Facades\Http;

class EmbeddingService implements EmbeddingServiceInterface
{
    private string $apiKey;
    private string $baseUrl = 'https://api.openai.com/v1';
    private string $model;

    public function __construct(string $apiKey, string $model = 'text-embedding-3-small')
    {
        $this->apiKey = $apiKey;
        $this->model = $model;
    }

    public function generate(string $text): array
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/embeddings", [
            'input' => $text,
            'model' => $this->model,
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException(
                "OpenAI API error: {$response->body()}"
            );
        }

        $data = $response->json();
        return $data['data'][0]['embedding'] ?? [];
    }

    public function generateBatch(array $texts): array
    {
        // Process in batches of 100 (OpenAI limit)
        $batches = array_chunk($texts, 100);
        $allEmbeddings = [];

        foreach ($batches as $batch) {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/embeddings", [
                'input' => $batch,
                'model' => $this->model,
            ]);

            if (!$response->successful()) {
                throw new \RuntimeException(
                    "OpenAI API error: {$response->body()}"
                );
            }

            $data = $response->json();
            foreach ($data['data'] ?? [] as $item) {
                $allEmbeddings[] = $item['embedding'];
            }
        }

        return $allEmbeddings;
    }

    public function similarity(array $vectorA, array $vectorB): float
    {
        if (count($vectorA) !== count($vectorB)) {
            throw new \InvalidArgumentException('Vectors must have the same dimension');
        }

        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        for ($i = 0; $i < count($vectorA); $i++) {
            $dotProduct += $vectorA[$i] * $vectorB[$i];
            $normA += $vectorA[$i] ** 2;
            $normB += $vectorB[$i] ** 2;
        }

        $denominator = sqrt($normA) * sqrt($normB);

        if ($denominator == 0) {
            return 0.0;
        }

        return $dotProduct / $denominator;
    }

    public function findSimilar(array $queryVector, array $candidates, int $topK = 5): array
    {
        $scores = [];

        foreach ($candidates as $candidate) {
            $similarity = $this->similarity($queryVector, $candidate['vector']);
            $scores[] = [
                'id' => $candidate['id'],
                'score' => $similarity,
            ];
        }

        // Sort by score descending
        usort($scores, fn($a, $b) => $b['score'] <=> $a['score']);

        // Return top K
        return array_slice($scores, 0, $topK);
    }

    /**
     * Chunk text into smaller pieces for embedding
     */
    public function chunkText(string $text, int $maxChunkSize = 1000, int $overlap = 100): array
    {
        $chunks = [];
        $length = strlen($text);
        $start = 0;

        while ($start < $length) {
            $end = min($start + $maxChunkSize, $length);
            
            // Try to break at sentence or word boundary
            if ($end < $length) {
                // Look for sentence ending
                $sentenceEnd = strpos($text, '. ', $end - $overlap);
                if ($sentenceEnd !== false && $sentenceEnd < $end + 100) {
                    $end = $sentenceEnd + 1;
                } else {
                    // Look for word boundary
                    $spacePos = strrpos(substr($text, $start, $maxChunkSize), ' ');
                    if ($spacePos !== false) {
                        $end = $start + $spacePos;
                    }
                }
            }

            $chunks[] = trim(substr($text, $start, $end - $start));
            $start = $end - $overlap;
        }

        return $chunks;
    }
}
