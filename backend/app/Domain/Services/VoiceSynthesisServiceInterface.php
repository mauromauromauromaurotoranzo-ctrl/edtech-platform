<?php

declare(strict_types=1);

namespace App\Domain\Services;

interface VoiceSynthesisServiceInterface
{
    /**
     * Synthesize text to speech
     * 
     * @return array{audioUrl: string, durationSeconds: float, charactersUsed: int}
     * @throws \RuntimeException If synthesis fails
     */
    public function synthesize(string $text, string $voiceId, array $options = []): array;
    
    /**
     * Synthesize with streaming response
     * 
     * @param callable(string $audioChunk): void $onChunk
     */
    public function synthesizeStream(string $text, string $voiceId, callable $onChunk, array $options = []): void;
    
    /**
     * Clone a voice from audio samples
     * 
     * @param string[] $sampleUrls URLs to audio samples
     * @return array{voiceId: string, name: string}
     */
    public function cloneVoice(array $sampleUrls, string $name, ?string $description = null): array;
    
    /**
     * Delete a cloned voice
     */
    public function deleteVoice(string $voiceId): void;
    
    /**
     * List available voices
     * @return array<int, array{voiceId: string, name: string, category: string}>
     */
    public function listVoices(): array;
}
