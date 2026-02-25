<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Infrastructure\Persistence\Eloquent\StudentModel;
use App\Infrastructure\Persistence\Eloquent\InstructorModel;
use App\Infrastructure\Persistence\Eloquent\KnowledgeBaseModel;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create instructor
        $user = StudentModel::factory()->instructor()->create([
            'name' => 'Prof. Carlos Rodríguez',
            'email' => 'carlos@example.com',
        ]);

        $instructor = InstructorModel::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'expertise_areas' => ['Economía', 'Finanzas', 'Macroeconomía'],
            'bio' => 'Economista con 15 años de experiencia docente. Especializado en economía aplicada y finanzas personales.',
            'voice_clone_id' => null,
            'is_verified' => true,
        ]);

        // Create knowledge bases
        KnowledgeBaseModel::create([
            'id' => (string) Str::uuid(),
            'instructor_id' => $instructor->id,
            'title' => 'Economía Básica para Principiantes',
            'description' => 'Aprende los fundamentos de la economía de forma interactiva y práctica.',
            'slug' => 'economia-basica-principiantes',
            'status' => 'published',
            'settings' => [
                'public_access' => true,
                'pricing_model' => 'subscription',
                'monthly_price' => 19.99,
            ],
        ]);

        KnowledgeBaseModel::create([
            'id' => (string) Str::uuid(),
            'instructor_id' => $instructor->id,
            'title' => 'Macroeconomía Avanzada',
            'description' => 'Análisis profundo de políticas monetarias, fiscal y crecimiento económico.',
            'slug' => 'macroeconomia-avanzada',
            'status' => 'published',
            'settings' => [
                'public_access' => true,
                'pricing_model' => 'subscription',
                'monthly_price' => 29.99,
            ],
        ]);

        // Create demo student
        StudentModel::factory()->create([
            'name' => 'Ana García',
            'email' => 'ana@example.com',
        ]);

        $this->command->info('Demo data created successfully!');
    }
}
