<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ChallengeController;
use App\Http\Controllers\Api\KnowledgeBaseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Chat
    Route::post('/chat', [ChatController::class, 'sendMessage']);
    Route::get('/conversations', [ChatController::class, 'getConversations']);
    
    // Challenges
    Route::get('/challenge/daily', [ChallengeController::class, 'getDaily']);
    Route::post('/challenge/answer', [ChallengeController::class, 'submitAnswer']);
    Route::get('/leaderboard', [ChallengeController::class, 'getLeaderboard']);
    
    // Knowledge Base (instructor only)
    Route::middleware('role:instructor')->group(function () {
        Route::apiResource('knowledge-bases', KnowledgeBaseController::class);
    });
});
