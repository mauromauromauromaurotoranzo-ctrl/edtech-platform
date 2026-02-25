<?php

declare(strict_types=1);

namespace App\Infrastructure\ExternalServices\ElevenLabs;

use App\Domain\Services\VoiceSynthesisServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ElevenLabsService implements VoiceSynthesisServiceInterface
{
    private string $apiKey;
    private string $baseUrl = 'https://api.elevenlabs.io/v1';

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function synthesize(string $text, string $voiceId, array $options = []): array
    {
        $modelId = $options['model_id'] ?? 'eleven_multilingual_v2';
        $stability = $options['stability'] ?? 0.5;
        $similarityBoost = $options['similarity_boost'] ?? 0.75;
        $style = $options['style'] ?? 0.0;
        $useSpeakerBoost = $options['use_speaker_boost'] ?? true;

        $response = Http::withHeaders([
            'xi-api-key' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/text-to-speech/{$voiceId}", [
            'text' => $text,
            'model_id' => $modelId,
            'voice_settings' => [
                'stability' => $stability,
                'similarity_boost' => $similarityBoost,
                'style' => $style,
                'use_speaker_boost' => $useSpeakerBoost,
            ],
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException(
                "ElevenLabs API error: {$response->body()}"
            );
        }

        // Save audio file
        $audioContent = $response->body();
        $filename = 'audio/' . uniqid('tts_', true) . '.mp3';
        Storage::disk('public')->put($filename, $audioContent);

        // Estimate duration (rough approximation: ~130 chars per 10 seconds)
        $estimatedDuration = strlen($text) / 13;

        return [
            'audioUrl' => Storage::disk('public')->url($filename),
            'durationSeconds' => round($estimatedDuration, 2),
            'charactersUsed' => strlen($text),
        ];
    }

    public function synthesizeStream(string $text, string $voiceId, callable $onChunk, array $options = []): void
    {
        $modelId = $options['model_id'] ?? 'eleven_multilingual_v2';
        $stability = $options['stability'] ?? 0.5;
        $similarityBoost = $options['similarity_boost'] ?? 0.75;

        $response = Http::withHeaders([
            'xi-api-key' => $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'audio/mpeg',
        ])->post("{$this->baseUrl}/text-to-speech/{$voiceId}/stream", [
            'text' => $text,
            'model_id' => $modelId,
            'voice_settings' => [
                'stability' => $stability,
                'similarity_boost' => $similarityBoost,
            ],
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException(
                "ElevenLabs API error: {$response->body()}"
            );
        }

        // Stream chunks to callback
        $chunkSize = 8192; // 8KB chunks
        $content = $response->body();
        $offset = 0;

        while ($offset < strlen($content)) {
            $chunk = substr($content, $offset, $chunkSize);
            $onChunk($chunk);
            $offset += $chunkSize;
        }
    }

    public function cloneVoice(array $sampleUrls, string $name, ?string $description = null): array
    {
        // Download samples and prepare multipart request
        $files = [];
        foreach ($sampleUrls as $index => $url) {
            $content = file_get_contents($url);
            $tempPath = tempnam(sys_get_temp_dir(), 'voice_sample_');
            file_put_contents($tempPath, $content);
            $files["files[{$index}]"] = fopen($tempPath, 'r');
        }

        $response = Http::withHeaders([
            'xi-api-key' => $this->apiKey,
        ])->attach($files)->post("{$this->baseUrl}/voices/add", [
            'name' => $name,
            'description' => $description ?? "Cloned voice for {$name}",
            'labels' => json_encode(['type' => 'instructor']),
        ]);

        // Clean up temp files
        foreach ($files as $file) {
            if (is_resource($file)) {
                fclose($file);
            }
        }

        if (!$response->successful()) {
            throw new \RuntimeException(
                "ElevenLabs API error: {$response->body()}"
            );
        }

        $data = $response->json();

        return [
            'voiceId' => $data['voice_id'],
            'name' => $name,
        ];
    }

    public function deleteVoice(string $voiceId): void
    {
        $response = Http::withHeaders([
            'xi-api-key' => $this->apiKey,
        ])->delete("{$this->baseUrl}/voices/{$voiceId}");

        if (!$response->successful()) {
            throw new \RuntimeException(
                "ElevenLabs API error: {$response->body()}"
            );
        }
    }

    public function listVoices(): array
    {
        $response = Http::withHeaders([
            'xi-api-key' => $this->apiKey,
        ])->get("{$this->baseUrl}/voices");

        if (!$response->successful()) {
            throw new \RuntimeException(
                "ElevenLabs API error: {$response->body()}"
            );
        }

        $data = $response->json();
        $voices = [];

        foreach ($data['voices'] ?? [] as $voice) {
            $voices[] = [
                'voiceId' => $voice['voice_id'],
                'name' => $voice['name'],
                'category' => $voice['category'] ?? 'cloned',
            ];
        }

        return $voices;
    }

    /**
     * Get voice settings for a specific voice
     */
    public function getVoiceSettings(string $voiceId): array
    {
        $response = Http::withHeaders([
            'xi-api-key' => $this->apiKey,
        ])->get("{$this->baseUrl}/voices/{$voiceId}/settings");

        if (!$response->successful()) {
            throw new \RuntimeException(
                "ElevenLabs API error: {$response->body()}"
            );
        }

        return $response->json();
    }

    /**
     * Edit voice settings
     */
    public function editVoiceSettings(string $voiceId, array $settings): void
    {
        $response = Http::withHeaders([
            'xi-api-key' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/voices/{$voiceId}/settings/edit", $settings);

        if (!$response->successful()) {
            throw new \RuntimeException(
                "ElevenLabs API error: {$response->body()}"
            );
        }
    }
}
