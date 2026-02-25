<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Services\RAGService;
use App\Application\Services\ChallengeEvaluatorService;
use App\Application\Services\ChallengeGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    public function __construct(
        private RAGService $ragService,
    ) {}

    public function sendMessage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'knowledge_base_id' => 'required|integer',
            'message' => 'required|string',
            'conversation_id' => 'nullable|integer',
            'mode' => 'nullable|string|in:tutor,quiz,summary,storytelling',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $studentId = auth()->id();
        
        $result = $this->ragService->generateResponse(
            studentId: $studentId,
            knowledgeBaseId: $request->input('knowledge_base_id'),
            query: $request->input('message'),
            conversationId: $request->input('conversation_id'),
            mode: $request->input('mode', 'tutor'),
        );

        return response()->json([
            'response' => $result['response'],
            'conversation_id' => $result['conversation_id'],
            'tokens_used' => $result['tokens_used'],
        ]);
    }

    public function getConversations(): JsonResponse
    {
        $studentId = auth()->id();
        // Implementation would fetch from repository
        return response()->json([]);
    }
}
