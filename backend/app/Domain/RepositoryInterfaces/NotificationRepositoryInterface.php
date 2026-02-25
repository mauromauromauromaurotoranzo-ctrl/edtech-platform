<?php

declare(strict_types=1);

namespace App\Domain\RepositoryInterfaces;

use App\Domain\Entities\Notification;
use App\Domain\Entities\NotificationChannel;

interface NotificationRepositoryInterface
{
    public function findById(int $id): ?Notification;
    
    /**
     * @return Notification[]
     */
    public function findAll(): array;
    
    /**
     * @return Notification[]
     */
    public function findByStudentId(int $studentId): array;
    
    /**
     * @return Notification[]
     */
    public function findByStatus(string $status): array;
    
    /**
     * @return Notification[]
     */
    public function findPending(): array;
    
    /**
     * @return Notification[]
     */
    public function findFailed(): array;
    
    public function save(Notification $notification): void;
    
    public function delete(int $id): void;
}
