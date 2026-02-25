<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\Services\InstructorVoiceService;
use App\Application\Services\RAGService;
use App\Domain\RepositoryInterfaces\ContentChunkRepositoryInterface;
use App\Domain\RepositoryInterfaces\ConversationRepositoryInterface;
use App\Domain\RepositoryInterfaces\InstructorVoiceRepositoryInterface;
use App\Domain\Services\EmbeddingServiceInterface;
use App\Domain\Services\LLMServiceInterface;
use App\Domain\Services\VoiceSynthesisServiceInterface;
use App\Infrastructure\ExternalServices\ElevenLabs\ElevenLabsService;
use App\Infrastructure\ExternalServices\OpenAI\EmbeddingService;
use App\Infrastructure\ExternalServices\OpenRouter\OpenRouterService;
use App\Infrastructure\Persistence\Eloquent\ContentChunkModel;
use App\Infrastructure\Persistence\Eloquent\ContentChunkRepository;
use App\Infrastructure\Persistence\Eloquent\ConversationModel;
use App\Infrastructure\Persistence\Eloquent\ConversationRepository;
use App\Infrastructure\Persistence\Eloquent\InstructorVoiceModel;
use App\Infrastructure\Persistence\Eloquent\InstructorVoiceRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AIServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repository bindings
        $this->app->bind(ContentChunkRepositoryInterface::class, function ($app) {
            return new ContentChunkRepository(new ContentChunkModel());
        });

        $this->app->bind(InstructorVoiceRepositoryInterface::class, function ($app) {
            return new InstructorVoiceRepository(new InstructorVoiceModel());
        });

        // AI Service bindings
        $this->app->bind(LLMServiceInterface::class, function ($app) {
            $apiKey = config('services.openrouter.api_key');
            if (!$apiKey) {
                throw new \RuntimeException('OpenRouter API key not configured');
            }
            return new OpenRouterService(
                apiKey: $apiKey,
                defaultModel: config('services.openrouter.model', 'anthropic/claude-3.5-sonnet')
            );
        });

        $this->app->bind(EmbeddingServiceInterface::class, function ($app) {
            $apiKey = config('services.openai.api_key');
            if (!$apiKey) {
                throw new \RuntimeException('OpenAI API key not configured');
            }
            return new EmbeddingService(
                apiKey: $apiKey,
                model: config('services.openai.embedding_model', 'text-embedding-3-small')
            );
        });

        $this->app->bind(VoiceSynthesisServiceInterface::class, function ($app) {
            $apiKey = config('services.elevenlabs.api_key');
            if (!$apiKey) {
                throw new \RuntimeException('ElevenLabs API key not configured');
            }
            return new ElevenLabsService(apiKey: $apiKey);
        });

        // Application Services
        $this->app->singleton(RAGService::class, function ($app) {
            return new RAGService(
                llmService: $app->make(LLMServiceInterface::class),
                embeddingService: $app->make(EmbeddingServiceInterface::class),
                chunkRepository: $app->make(ContentChunkRepositoryInterface::class),
                conversationRepository: $app->make(ConversationRepositoryInterface::class),
                logger: Log::channel(),
            );
        });

        $this->app->singleton(InstructorVoiceService::class, function ($app) {
            return new InstructorVoiceService(
                voiceSynthesis: $app->make(VoiceSynthesisServiceInterface::class),
                voiceRepository: $app->make(InstructorVoiceRepositoryInterface::class),
                logger: Log::channel(),
            );
        });
    }

    public function boot(): void
    {
        //
    }
}
