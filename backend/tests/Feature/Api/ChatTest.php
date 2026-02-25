<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Infrastructure\Persistence\Eloquent\UserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_send_message(): void
    {
        $user = UserModel::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/chat', [
            'knowledge_base_id' => 1,
            'message' => 'Hello',
        ]);

        // Would need mock for RAG service
        $response->assertStatus(200);
    }

    public function test_guest_cannot_send_message(): void
    {
        $response = $this->postJson('/api/chat', [
            'knowledge_base_id' => 1,
            'message' => 'Hello',
        ]);

        $response->assertStatus(401);
    }

    public function test_message_requires_knowledge_base(): void
    {
        $user = UserModel::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/chat', [
            'message' => 'Hello',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['knowledge_base_id']);
    }
}
