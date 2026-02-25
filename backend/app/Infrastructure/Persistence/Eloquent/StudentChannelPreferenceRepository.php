<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Entities\NotificationChannel;
use App\Domain\Entities\StudentChannelPreference;
use App\Domain\RepositoryInterfaces\StudentChannelPreferenceRepositoryInterface;

class StudentChannelPreferenceRepository implements StudentChannelPreferenceRepositoryInterface
{
    public function __construct(
        private StudentChannelPreferenceModel $model
    ) {}

    public function findById(int $id): ?StudentChannelPreference
    {
        $record = $this->model->find($id);
        return $record ? $this->toEntity($record) : null;
    }

    public function findByStudentId(int $studentId): ?StudentChannelPreference
    {
        $record = $this->model->where('student_id', $studentId)->first();
        return $record ? $this->toEntity($record) : null;
    }

    public function findAll(): array
    {
        return $this->model->all()->map(fn($r) => $this->toEntity($r))->toArray();
    }

    public function save(StudentChannelPreference $preference): void
    {
        $data = [
            'student_id' => $preference->getStudentId(),
            'priority_order' => array_map(
                fn(NotificationChannel $c) => $c->value,
                $preference->getPriorityOrder()
            ),
            'whatsapp_number' => $preference->getWhatsappNumber(),
            'telegram_chat_id' => $preference->getTelegramChatId(),
            'email_address' => $preference->getEmailAddress(),
            'notifications_enabled' => $preference->isNotificationsEnabled(),
        ];

        if ($preference->getId()) {
            $this->model->where('id', $preference->getId())->update($data);
        } else {
            $this->model->create($data);
        }
    }

    public function delete(int $id): void
    {
        $this->model->destroy($id);
    }

    private function toEntity(StudentChannelPreferenceModel $model): StudentChannelPreference
    {
        $priorityOrder = array_map(
            fn(string $c) => NotificationChannel::from($c),
            $model->priority_order ?? ['telegram', 'email', 'whatsapp']
        );

        return new StudentChannelPreference(
            id: $model->id,
            studentId: $model->student_id,
            priorityOrder: $priorityOrder,
            whatsappNumber: $model->whatsapp_number,
            telegramChatId: $model->telegram_chat_id,
            emailAddress: $model->email_address,
            notificationsEnabled: $model->notifications_enabled,
            createdAt: $model->created_at->toDateTimeImmutable(),
            updatedAt: $model->updated_at->toDateTimeImmutable(),
        );
    }
}
