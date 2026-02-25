<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Entities\InstructorVoice;
use App\Domain\RepositoryInterfaces\InstructorVoiceRepositoryInterface;
use App\Domain\Services\VoiceSynthesisServiceInterface;
use App\Domain\ValueObjects\VoiceSettings;
use Illuminate\Support\Facades\Storage;
use Psr\Log\LoggerInterface;

class InstructorVoiceService
{
    public function __construct(
        private VoiceSynthesisServiceInterface $voiceSynthesis,
        private InstructorVoiceRepositoryInterface $voiceRepository,
        private LoggerInterface $logger,
    ) {}

    /**
     * Clone instructor voice from audio samples
     */
    public function cloneVoice(
        int $instructorId,
        array $sampleFiles,
        string $name,
        ?string $description = null,
        bool $setAsDefault = false
    ): InstructorVoice {
        try {
            // Upload samples to storage and get URLs
            $sampleUrls = [];
            foreach ($sampleFiles as $file) {
                $path = $file->store('voice-samples', 'public');
                $sampleUrls[] = Storage::disk('public')->url($path);
            }

            // Clone voice via ElevenLabs
            $result = $this->voiceSynthesis->cloneVoice($sampleUrls, $name, $description);

            // Create voice entity
            $voice = InstructorVoice::create(
                instructorId: $instructorId,
                voiceId: $result['voiceId'],
                name: $name,
                description: $description,
                settings: new VoiceSettings(),
                sampleUrl: $sampleUrls[0] ?? null,
                isDefault: $setAsDefault,
            );

            $this->voiceRepository->save($voice);

            $this->logger->info('Voice cloned successfully', [
                'instructor_id' => $instructorId,
                'voice_id' => $result['voiceId'],
            ]);

            return $voice;

        } catch (\Exception $e) {
            $this->logger->error('Voice cloning failed', [
                'instructor_id' => $instructorId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Synthesize text using instructor's voice
     */
    public function synthesize(
        int $instructorId,
        string $text,
        ?int $voiceId = null,
        ?VoiceSettings $customSettings = null
    ): array {
        // Get voice to use
        $voice = $this->resolveVoice($instructorId, $voiceId);
        
        if (!$voice) {
            throw new \RuntimeException('No voice found for instructor');
        }

        // Merge settings
        $settings = $customSettings ?? $voice->getSettings();

        try {
            $result = $this->voiceSynthesis->synthesize($text, $voice->getVoiceId(), [
                'stability' => $settings->getStability(),
                'similarity_boost' => $settings->getSimilarityBoost(),
                'style' => $settings->getStyle(),
                'use_speaker_boost' => $settings->useSpeakerBoost(),
            ]);

            $this->logger->info('Voice synthesis completed', [
                'instructor_id' => $instructorId,
                'voice_id' => $voice->getVoiceId(),
                'characters' => strlen($text),
            ]);

            return $result;

        } catch (\Exception $e) {
            $this->logger->error('Voice synthesis failed', [
                'instructor_id' => $instructorId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Synthesize with streaming
     */
    public function synthesizeStream(
        int $instructorId,
        string $text,
        callable $onChunk,
        ?int $voiceId = null
    ): void {
        $voice = $this->resolveVoice($instructorId, $voiceId);
        
        if (!$voice) {
            throw new \RuntimeException('No voice found for instructor');
        }

        $settings = $voice->getSettings();

        $this->voiceSynthesis->synthesizeStream($text, $voice->getVoiceId(), $onChunk, [
            'stability' => $settings->getStability(),
            'similarity_boost' => $settings->getSimilarityBoost(),
        ]);
    }

    /**
     * Delete a voice
     */
    public function deleteVoice(int $voiceEntityId): void
    {
        $voice = $this->voiceRepository->findById($voiceEntityId);
        
        if (!$voice) {
            throw new \InvalidArgumentException('Voice not found');
        }

        try {
            // Delete from ElevenLabs
            $this->voiceSynthesis->deleteVoice($voice->getVoiceId());
            
            // Delete from database
            $this->voiceRepository->delete($voiceEntityId);

            $this->logger->info('Voice deleted', [
                'voice_id' => $voice->getVoiceId(),
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Voice deletion failed', [
                'voice_id' => $voice->getVoiceId(),
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Update voice settings
     */
    public function updateVoiceSettings(int $voiceEntityId, VoiceSettings $settings): void
    {
        $voice = $this->voiceRepository->findById($voiceEntityId);
        
        if (!$voice) {
            throw new \InvalidArgumentException('Voice not found');
        }

        $voice->updateSettings($settings);
        $this->voiceRepository->save($voice);

        // Update on ElevenLabs if needed
        try {
            // Note: ElevenLabs doesn't support updating voice settings directly
            // Settings are applied per-synthesis request
        } catch (\Exception $e) {
            $this->logger->warning('Could not update voice settings on provider', [
                'voice_id' => $voice->getVoiceId(),
            ]);
        }
    }

    /**
     * Set default voice for instructor
     */
    public function setDefaultVoice(int $instructorId, int $voiceEntityId): void
    {
        $voice = $this->voiceRepository->findById($voiceEntityId);
        
        if (!$voice || $voice->getInstructorId() !== $instructorId) {
            throw new \InvalidArgumentException('Voice not found or does not belong to instructor');
        }

        // Unset current default
        $this->voiceRepository->unsetDefaultForInstructor($instructorId);
        
        // Set new default
        $voice->setAsDefault(true);
        $this->voiceRepository->save($voice);
    }

    /**
     * List all voices for an instructor
     */
    public function listVoices(int $instructorId): array
    {
        return $this->voiceRepository->findByInstructorId($instructorId);
    }

    /**
     * Get available system voices from ElevenLabs
     */
    public function listSystemVoices(): array
    {
        return $this->voiceSynthesis->listVoices();
    }

    /**
     * Resolve which voice to use
     */
    private function resolveVoice(int $instructorId, ?int $voiceId): ?InstructorVoice
    {
        if ($voiceId) {
            $voice = $this->voiceRepository->findById($voiceId);
            if ($voice && $voice->getInstructorId() === $instructorId) {
                return $voice;
            }
        }

        // Return default voice
        return $this->voiceRepository->findDefaultByInstructorId($instructorId);
    }
}
