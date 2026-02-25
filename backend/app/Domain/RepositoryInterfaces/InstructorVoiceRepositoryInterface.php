<?php

declare(strict_types=1);

namespace App\Domain\RepositoryInterfaces;

use App\Domain\Entities\InstructorVoice;

interface InstructorVoiceRepositoryInterface
{
    public function findById(int $id): ?InstructorVoice;
    
    /**
     * @return InstructorVoice[]
     */
    public function findAll(): array;
    
    /**
     * @return InstructorVoice[]
     */
    public function findByInstructorId(int $instructorId): array;
    
    public function findDefaultByInstructorId(int $instructorId): ?InstructorVoice;
    
    public function findByVoiceId(string $voiceId): ?InstructorVoice;
    
    public function save(InstructorVoice $voice): void;
    
    public function delete(int $id): void;
    
    /**
     * Unset default flag for all voices of an instructor
     */
    public function unsetDefaultForInstructor(int $instructorId): void;
}
