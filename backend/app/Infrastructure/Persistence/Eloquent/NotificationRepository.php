<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Entities\Notification;
use App\Domain\Entities\NotificationChannel;
use App\Domain\Entities\NotificationStatus;
use App\Domain\RepositoryInterfaces\NotificationRepositoryInterface;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function __construct(
        private NotificationModel $model
    ) {}

    public function findById(int $id): ?Notification
    {
        $record = $this->model->find($id);
        return $record ? $this->toEntity($record) : null;
    }

    public function findAll(): array
    {
        return $this->model->all()->map(fn($r) => $this->toEntity($r))->toArray();
    }

    public function findByStudentId(int $studentId): array
    {
        return $this->model->where('student_id', $studentId)
            ->get()
            ->map(fn($r) => $this->toEntity($r))
            ->toArray();
    }

    public function findByStatus(string $status): array
    {
        return $this->model->where('status', $status)
            ->get()
            ->map(fn($r) => $this->toEntity($r))
            ->toArray();
    }

    public function findPending(): array
    {
        return $this->findByStatus(NotificationStatus::PENDING->value);
    }

    public function findFailed(): array
    {
        return $this->findByStatus(NotificationStatus::FAILED->value);
    }

    public function save(Notification $notification): void
    {
        $data = [
            'student_id' => $notification->getStudentId(),
            'subject' => $notification->getSubject(),
            'content' => $notification->getContent(),
            'channel' => $notification->getChannel()->value,
            'status' => $notification->getStatus()->value,
            'external_id' => $notification->getExternalId(),
            'sent_at' => $notification->getSentAt(),
            'delivered_at' => $notification->getDeliveredAt(),
            'read_at' => $notification->getReadAt(),
            'error_message' => $notification->getErrorMessage(),
            'retry_count' => $notification->getRetryCount(),
        ];

        if ($notification->getId()) {
            $this->model->where('id', $notification->getId())->update($data);
        } else {
            $this->model->create($data);
        }
    }

    public function delete(int $id): void
    {
        $this->model->destroy($id);
    }

    private function toEntity(NotificationModel $model): Notification
    {
        return new Notification(
            id: $model->id,
            studentId: $model->student_id,
            subject: $model->subject,
            content: $model->content,
            channel: NotificationChannel::from($model->channel),
            status: NotificationStatus::from($model->status),
            externalId: $model->external_id,
            sentAt: $model->sent_at?->toDateTimeImmutable(),
            deliveredAt: $model->delivered_at?->toDateTimeImmutable(),
            readAt: $model->read_at?->toDateTimeImmutable(),
            errorMessage: $model->error_message,
            retryCount: $model->retry_count,
            createdAt: $model->created_at->toDateTimeImmutable(),
            updatedAt: $model->updated_at->toDateTimeImmutable(),
        );
    }
}
