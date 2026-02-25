<?php

declare(strict_types=1);

namespace App\Domain\RepositoryInterfaces;

use App\Domain\Entities\StudentChannelPreference;

interface StudentChannelPreferenceRepositoryInterface
{
    public function findById(int $id): ?StudentChannelPreference;
    
    public function findByStudentId(int $studentId): ?StudentChannelPreference;
    
    /**
     * @return StudentChannelPreference[]
     */
    public function findAll(): array;
    
    public function save(StudentChannelPreference $preference): void;
    
    public function delete(int $id): void;
}
