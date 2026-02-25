<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Services\ChallengeEvaluatorService;
use App\Application\Services\ChallengeGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChallengeController extends Controller
{
    public function __construct(
        private ChallengeGeneratorService $generatorService,
        private ChallengeEvaluatorService $evaluatorService,
    ) {}

    public function getDaily(Request $request): JsonResponse
    {
        $studentId = auth()->id();
        $knowledgeBaseId = $request->input('knowledge_base_id');

        $status = $this->evaluatorService->getDailyStatus(
            $studentId,
            (int) $knowledgeBaseId
        );

        return response()->json($status);
    }

    public function submitAnswer(Request $request): JsonResponse
    {
        $request->validate([
            'challenge_id' => 'required|integer',
            'answer' => 'required|string',
        ]);

        $result = $this->evaluatorService->submitAnswer(
            $request->input('challenge_id'),
            $request->input('answer')
        );

        return response()->json($result);
    }

    public function getLeaderboard(Request $request): JsonResponse
    {
        $knowledgeBaseId = $request->input('knowledge_base_id');
        $leaderboard = $this->evaluatorService->getLeaderboard((int) $knowledgeBaseId);

        return response()->json($leaderboard);
    }
}
