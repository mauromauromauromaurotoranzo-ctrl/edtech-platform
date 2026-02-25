<?php

declare(strict_types=1);

namespace App\Domain\Services;

interface EmbeddingServiceInterface
{
    /**
     * Generate embeddings for a text
     * 
     * @return array<float> Vector of embeddings
     * @throws \RuntimeException If generation fails
     */
    public function generate(string $text): array;
    
    /**
     * Generate embeddings for multiple texts in batch
     * 
     * @param string[] $texts
     * @return array<array<float>> Array of embedding vectors
     */
    public function generateBatch(array $texts): array;
    
    /**
     * Calculate cosine similarity between two vectors
     */
    public function similarity(array $vectorA, array $vectorB): float;
    
    /**
     * Find most similar vectors from a collection
     * 
     * @param array<float> $queryVector
     * @param array<array{id: int, vector: array<float>}> $candidates
     * @param int $topK Number of results to return
     * @return array<array{id: int, score: float}>
     */
    public function findSimilar(array $queryVector, array $candidates, int $topK = 5): array;
}
