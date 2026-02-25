<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Entities\Conversation;
use App\Domain\Entities\Message;
use App\Domain\Entities\MessageRole;
use App\Domain\RepositoryInterfaces\ContentChunkRepositoryInterface;
use App\Domain\RepositoryInterfaces\ConversationRepositoryInterface;
use App\Domain\Services\EmbeddingServiceInterface;
use App\Domain\Services\LLMServiceInterface;
use Psr\Log\LoggerInterface;

class RAGService
{
    public function __construct(
        private LLMServiceInterface $llmService,
        private EmbeddingServiceInterface $embeddingService,
        private ContentChunkRepositoryInterface $chunkRepository,
        private ConversationRepositoryInterface $conversationRepository,
        private LoggerInterface $logger,
    ) {}

    /**
     * Generate a response using RAG (Retrieval Augmented Generation)
     */
    public function generateResponse(
        int $studentId,
        int $knowledgeBaseId,
        string $query,
        ?int $conversationId = null,
        string $mode = 'tutor',
    ): array {
        // 1. Retrieve relevant chunks
        $relevantChunks = $this->retrieveRelevantChunks($knowledgeBaseId, $query);
        
        if (empty($relevantChunks)) {
            return [
                'response' => 'Lo siento, no tengo información sobre ese tema en mi base de conocimientos. ¿Puedes reformular tu pregunta?',
                'chunks_used' => [],
                'tokens_used' => 0,
            ];
        }

        // 2. Get or create conversation
        $conversation = $this->getOrCreateConversation($studentId, $knowledgeBaseId, $conversationId);

        // 3. Build messages with context
        $messages = $this->buildMessages($conversation, $query, $relevantChunks, $mode);

        // 4. Generate response
        try {
            $result = $this->llmService->chat($messages, [
                'temperature' => $this->getTemperatureForMode($mode),
            ]);

            // 5. Save to conversation
            $userMessage = Message::create(MessageRole::USER, $query);
            $assistantMessage = Message::create(
                MessageRole::ASSISTANT, 
                $result['text'],
                $result['tokensUsed']
            );
            
            $conversation->addMessage($userMessage);
            $conversation->addMessage($assistantMessage);
            
            // Track referenced chunks
            foreach ($relevantChunks as $chunk) {
                $conversation->addChunkReference((string) $chunk['id']);
            }
            
            $this->conversationRepository->save($conversation);

            return [
                'response' => $result['text'],
                'chunks_used' => array_map(fn($c) => $c['id'], $relevantChunks),
                'tokens_used' => $result['tokensUsed'],
                'conversation_id' => $conversation->getId(),
            ];

        } catch (\Exception $e) {
            $this->logger->error('RAG generation failed', [
                'error' => $e->getMessage(),
                'student_id' => $studentId,
                'knowledge_base_id' => $knowledgeBaseId,
            ]);
            
            throw $e;
        }
    }

    /**
     * Generate streaming response
     */
    public function generateResponseStream(
        int $studentId,
        int $knowledgeBaseId,
        string $query,
        callable $onChunk,
        ?int $conversationId = null,
        string $mode = 'tutor',
    ): void {
        $relevantChunks = $this->retrieveRelevantChunks($knowledgeBaseId, $query);
        $conversation = $this->getOrCreateConversation($studentId, $knowledgeBaseId, $conversationId);
        $messages = $this->buildMessages($conversation, $query, $relevantChunks, $mode);

        $fullResponse = '';
        
        $this->llmService->chatStream($messages, function ($chunk) use ($onChunk, &$fullResponse) {
            $fullResponse .= $chunk;
            $onChunk($chunk);
        }, [
            'temperature' => $this->getTemperatureForMode($mode),
        ]);

        // Save conversation after streaming
        $userMessage = Message::create(MessageRole::USER, $query);
        $assistantMessage = Message::create(MessageRole::ASSISTANT, $fullResponse);
        
        $conversation->addMessage($userMessage);
        $conversation->addMessage($assistantMessage);
        
        foreach ($relevantChunks as $chunk) {
            $conversation->addChunkReference((string) $chunk['id']);
        }
        
        $this->conversationRepository->save($conversation);
    }

    /**
     * Index new content chunks for a knowledge base
     */
    public function indexContent(int $knowledgeBaseId, string $content, array $metadata = []): array
    {
        // Chunk the content
        $chunks = $this->embeddingService->chunkText($content);
        $createdChunks = [];

        foreach ($chunks as $index => $chunkText) {
            $chunk = \App\Domain\Entities\ContentChunk::create(
                knowledgeBaseId: $knowledgeBaseId,
                content: $chunkText,
                sourceType: $metadata['source_type'] ?? 'text',
                pageNum: $metadata['page_num'] ?? null,
                section: $metadata['section'] ?? null,
                contextWindow: $metadata['context_window'] ?? null,
                metadata: array_merge($metadata, ['chunk_index' => $index]),
            );

            $this->chunkRepository->save($chunk);
            $createdChunks[] = $chunk;
        }

        return $createdChunks;
    }

    /**
     * Generate embeddings for pending chunks
     */
    public function processPendingEmbeddings(int $batchSize = 50): int
    {
        $pendingChunks = $this->chunkRepository->findPendingEmbeddings($batchSize);
        
        if (empty($pendingChunks)) {
            return 0;
        }

        $texts = array_map(fn($chunk) => $chunk->getContent(), $pendingChunks);
        $embeddings = $this->embeddingService->generateBatch($texts);

        foreach ($pendingChunks as $index => $chunk) {
            if (isset($embeddings[$index])) {
                $chunk->setEmbeddingVector($embeddings[$index]);
                $this->chunkRepository->save($chunk);
            }
        }

        return count($pendingChunks);
    }

    /**
     * Retrieve relevant chunks using semantic search
     */
    private function retrieveRelevantChunks(int $knowledgeBaseId, string $query, int $topK = 5): array
    {
        // Generate query embedding
        $queryEmbedding = $this->embeddingService->generate($query);
        
        // Get all chunks with embeddings for this knowledge base
        $chunks = $this->chunkRepository->findWithEmbeddingsByKnowledgeBaseId($knowledgeBaseId);
        
        if (empty($chunks)) {
            return [];
        }

        // Prepare candidates for similarity search
        $candidates = array_map(fn($chunk) => [
            'id' => $chunk->getId(),
            'vector' => $chunk->getEmbeddingVector(),
            'content' => $chunk->getContent(),
        ], $chunks);

        // Find similar chunks
        $similar = $this->embeddingService->findSimilar($queryEmbedding, $candidates, $topK);
        
        // Filter by relevance threshold (0.7 cosine similarity)
        $relevant = array_filter($similar, fn($item) => $item['score'] > 0.7);
        
        // Map back to chunk data
        $chunkMap = [];
        foreach ($chunks as $chunk) {
            $chunkMap[$chunk->getId()] = [
                'id' => $chunk->getId(),
                'content' => $chunk->getContent(),
                'source_type' => $chunk->getSourceType(),
                'page_num' => $chunk->getPageNum(),
                'section' => $chunk->getSection(),
            ];
        }

        $result = [];
        foreach ($relevant as $item) {
            if (isset($chunkMap[$item['id']])) {
                $result[] = $chunkMap[$item['id']];
            }
        }

        return $result;
    }

    private function getOrCreateConversation(
        int $studentId,
        int $knowledgeBaseId,
        ?int $conversationId
    ): Conversation {
        if ($conversationId) {
            $conversation = $this->conversationRepository->findById($conversationId);
            if ($conversation) {
                return $conversation;
            }
        }

        return Conversation::create($studentId, $knowledgeBaseId);
    }

    private function buildMessages(
        Conversation $conversation,
        string $query,
        array $relevantChunks,
        string $mode
    ): array {
        // Build context from chunks
        $contextText = implode("\n\n", array_map(
            fn($chunk) => "[Fuente: {$chunk['source_type']}]\n{$chunk['content']}",
            $relevantChunks
        ));

        // System prompt based on mode
        $systemPrompts = [
            'tutor' => "Eres un tutor experto y paciente. Responde basándote ÚNICAMENTE en el contexto proporcionado. Explica los conceptos de manera clara y didáctica. Si la respuesta no está en el contexto, indica que no tienes esa información.",
            'quiz' => "Eres un evaluador. Genera preguntas de opción múltiple basadas en el contexto proporcionado. Evalúa las respuestas del estudiante y proporciona retroalimentación constructiva.",
            'summary' => "Eres un sintetizador experto. Resume la información del contexto de manera concisa y estructurada. Destaca los puntos clave y conceptos importantes.",
            'storytelling' => "Eres un narrador cautivador. Transforma el contenido del contexto en una historia envolvente y memorable. Usa analogías y ejemplos prácticos.",
        ];

        $systemPrompt = $systemPrompts[$mode] ?? $systemPrompts['tutor'];

        $messages = [
            [
                'role' => 'system',
                'content' => "{$systemPrompt}\n\nContexto disponible:\n{$contextText}"
            ],
        ];

        // Add conversation history (last 10 messages)
        $history = array_slice($conversation->getMessages(), -10);
        foreach ($history as $msg) {
            $messages[] = [
                'role' => $msg->getRole()->value,
                'content' => $msg->getContent(),
            ];
        }

        // Add current query
        $messages[] = [
            'role' => 'user',
            'content' => $query,
        ];

        return $messages;
    }

    private function getTemperatureForMode(string $mode): float
    {
        return match($mode) {
            'tutor' => 0.5,
            'quiz' => 0.3,
            'summary' => 0.4,
            'storytelling' => 0.7,
            default => 0.5,
        };
    }
}
