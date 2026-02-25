<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Entities\DailyChallenge;
use App\Domain\RepositoryInterfaces\ContentChunkRepositoryInterface;
use App\Domain\RepositoryInterfaces\DailyChallengeRepositoryInterface;
use App\Domain\Services\LLMServiceInterface;
use App\Domain\ValueObjects\ChallengeType;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;

class ChallengeGeneratorService
{
    public function __construct(
        private LLMServiceInterface $llmService,
        private ContentChunkRepositoryInterface $chunkRepository,
        private DailyChallengeRepositoryInterface $challengeRepository,
        private LoggerInterface $logger,
    ) {}

    /**
     * Generate a daily challenge for a student
     */
    public function generateChallenge(
        int $studentId,
        int $knowledgeBaseId,
        ChallengeType $type = null,
        int $difficulty = 2, // 1-5 scale
    ): ?DailyChallenge {
        try {
            // Get random content chunk for context
            $chunks = $this->chunkRepository->findByKnowledgeBaseId($knowledgeBaseId);
            
            if (empty($chunks)) {
                $this->logger->warning('No content chunks found for challenge generation', [
                    'knowledge_base_id' => $knowledgeBaseId,
                ]);
                return null;
            }

            // Select random chunk
            $randomChunk = $chunks[array_rand($chunks)];
            $context = $randomChunk->getContent();

            // Determine challenge type if not specified
            $type ??= $this->selectRandomType();

            // Generate challenge using AI
            $challengeData = $this->generateWithAI($type, $context, $difficulty);

            if (!$challengeData) {
                return null;
            }

            // Create challenge entity
            $challenge = DailyChallenge::create(
                studentId: $studentId,
                knowledgeBaseId: $knowledgeBaseId,
                type: $type,
                title: $challengeData['title'],
                content: $challengeData['content'],
                correctAnswer: $challengeData['correct_answer'] ?? null,
                options: $challengeData['options'] ?? [],
                explanation: $challengeData['explanation'] ?? '',
                points: $this->calculatePoints($type, $difficulty),
                scheduledFor: new DateTimeImmutable(),
            );

            // Save to database
            $this->challengeRepository->save($challenge);

            $this->logger->info('Challenge generated successfully', [
                'student_id' => $studentId,
                'type' => $type->value,
                'knowledge_base_id' => $knowledgeBaseId,
            ]);

            return $challenge;

        } catch (\Exception $e) {
            $this->logger->error('Challenge generation failed', [
                'student_id' => $studentId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Generate batch challenges for multiple students
     */
    public function generateBatch(array $studentIds, int $knowledgeBaseId): array
    {
        $generated = [];
        
        foreach ($studentIds as $studentId) {
            // Check if student already has a challenge for today
            $today = new DateTimeImmutable();
            $existing = $this->challengeRepository->findByStudentIdAndDate($studentId, $today);
            
            if (!$existing) {
                $challenge = $this->generateChallenge($studentId, $knowledgeBaseId);
                if ($challenge) {
                    $generated[] = $challenge;
                }
            }
        }

        return $generated;
    }

    /**
     * Generate challenge using AI
     */
    private function generateWithAI(ChallengeType $type, string $context, int $difficulty): ?array
    {
        $prompts = [
            ChallengeType::QUIZ->value => $this->buildQuizPrompt($context, $difficulty),
            ChallengeType::PUZZLE->value => $this->buildPuzzlePrompt($context, $difficulty),
            ChallengeType::SCENARIO->value => $this->buildScenarioPrompt($context, $difficulty),
            ChallengeType::FLASHCARD->value => $this->buildFlashcardPrompt($context, $difficulty),
            ChallengeType::CODE->value => $this->buildCodePrompt($context, $difficulty),
            ChallengeType::MATCHING->value => $this->buildMatchingPrompt($context, $difficulty),
        ];

        $prompt = $prompts[$type->value] ?? $prompts[ChallengeType::QUIZ->value];

        try {
            $response = $this->llmService->chat([
                ['role' => 'system', 'content' => 'Eres un generador de desafíos educativos. Responde ÚNICAMENTE con JSON válido.'],
                ['role' => 'user', 'content' => $prompt],
            ], [
                'temperature' => 0.7,
            ]);

            // Parse JSON response
            $content = $response['text'];
            
            // Extract JSON if wrapped in markdown
            if (preg_match('/```json\s*(.*?)\s*```/s', $content, $matches)) {
                $content = $matches[1];
            }

            $data = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->logger->error('Invalid JSON from AI', ['content' => $content]);
                return null;
            }

            return $data;

        } catch (\Exception $e) {
            $this->logger->error('AI generation failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function buildQuizPrompt(string $context, int $difficulty): string
    {
        $difficultyText = match($difficulty) {
            1 => 'muy fácil',
            2 => 'fácil',
            3 => 'medio',
            4 => 'difícil',
            5 => 'muy difícil',
        };

        return <<<PROMPT
Basado en el siguiente contenido, genera una pregunta de opción múltiple de nivel {$difficultyText}.

CONTEXTO:
{$context}

Responde con este formato JSON exacto:
{
    "title": "Título breve del desafío",
    "content": "La pregunta completa",
    "options": ["Opción A", "Opción B", "Opción C", "Opción D"],
    "correct_answer": "Opción correcta (debe estar en el array de options)",
    "explanation": "Explicación de por qué es la respuesta correcta"
}
PROMPT;
    }

    private function buildPuzzlePrompt(string $context, int $difficulty): string
    {
        return <<<PROMPT
Basado en el siguiente contenido, genera un rompecabezas lógico o problema para resolver.

CONTEXTO:
{$context}

Responde con este formato JSON exacto:
{
    "title": "Título del rompecabezas",
    "content": "Descripción del problema",
    "correct_answer": "Respuesta correcta (una palabra o número corto)",
    "explanation": "Explicación de la solución paso a paso"
}
PROMPT;
    }

    private function buildScenarioPrompt(string $context, int $difficulty): string
    {
        return <<<PROMPT
Basado en el siguiente contenido, genera un escenario práctico para analizar.

CONTEXTO:
{$context}

Responde con este formato JSON exacto:
{
    "title": "Título del escenario",
    "content": "Descripción detallada del caso o situación",
    "correct_answer": "Respuesta esperada (breve)",
    "explanation": "Análisis y solución recomendada"
}
PROMPT;
    }

    private function buildFlashcardPrompt(string $context, int $difficulty): string
    {
        return <<<PROMPT
Basado en el siguiente contenido, genera una tarjeta de memoria.

CONTEXTO:
{$context}

Responde con este formato JSON exacto:
{
    "title": "Concepto clave",
    "content": "Pregunta o concepto a recordar",
    "correct_answer": "Respuesta o definición",
    "explanation": "Información adicional útil"
}
PROMPT;
    }

    private function buildCodePrompt(string $context, int $difficulty): string
    {
        return <<<PROMPT
Basado en el siguiente contenido, genera un ejercicio de código.

CONTEXTO:
{$context}

Responde con este formato JSON exacto:
{
    "title": "Ejercicio de código",
    "content": "Instrucciones y código incompleto o con errores",
    "correct_answer": "Código correcto o salida esperada",
    "explanation": "Explicación de la solución"
}
PROMPT;
    }

    private function buildMatchingPrompt(string $context, int $difficulty): string
    {
        return <<<PROMPT
Basado en el siguiente contenido, genera un ejercicio de emparejamiento.

CONTEXTO:
{$context}

Responde con este formato JSON exacto:
{
    "title": "Empareja los conceptos",
    "content": "Instrucciones para el ejercicio",
    "options": ["Concepto 1", "Definición 1", "Concepto 2", "Definición 2", "Concepto 3", "Definición 3"],
    "correct_answer": "Concepto 1 - Definición 1, Concepto 2 - Definición 2, Concepto 3 - Definición 3",
    "explanation": "Explicación de las relaciones correctas"
}
PROMPT;
    }

    private function selectRandomType(): ChallengeType
    {
        $types = ChallengeType::cases();
        return $types[array_rand($types)];
    }

    private function calculatePoints(ChallengeType $type, int $difficulty): int
    {
        $basePoints = match($type) {
            ChallengeType::QUIZ => 10,
            ChallengeType::PUZZLE => 15,
            ChallengeType::SCENARIO => 12,
            ChallengeType::FLASHCARD => 8,
            ChallengeType::CODE => 20,
            ChallengeType::MATCHING => 12,
        };

        return $basePoints * $difficulty;
    }
}
