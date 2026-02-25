<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = \App\Infrastructure\Persistence\Eloquent\StudentModel::class;

    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'role' => 'student',
            'preferences' => [
                'learning_style' => fake()->randomElement(['visual', 'auditory', 'kinesthetic']),
                'difficulty_preference' => 'adaptive',
                'preferred_channels' => ['app'],
                'daily_challenges_enabled' => true,
                'reminder_time' => '09:00',
                'session_duration_minutes' => 30,
            ],
            'email_verified_at' => now(),
        ];
    }

    public function instructor(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'instructor',
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }
}
