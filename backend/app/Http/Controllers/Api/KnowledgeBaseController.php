<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KnowledgeBaseController extends Controller
{
    public function index(): JsonResponse
    {
        // Implementation would fetch from repository
        return response()->json([]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        return response()->json(['message' => 'Created'], 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(['id' => $id]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        return response()->json(['message' => 'Updated']);
    }

    public function destroy(int $id): JsonResponse
    {
        return response()->json(['message' => 'Deleted']);
    }
}
