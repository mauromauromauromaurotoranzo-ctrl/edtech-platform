<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Entities\InstructorVoice;
use App\Domain\RepositoryInterfaces\InstructorVoiceRepositoryInterface;
use App\Domain\ValueObjects\VoiceSettings;

class InstructorVoiceRepository implements InstructorVoiceRepositoryInterface
{
    public function __construct(
        private InstructorVoiceModel $model
    ) {}

    public function findById(int $id): ?InstructorVoice
    {
        $record = $this->model->find($id);
        return $record ? $this->toEntity($record) : null;
    }

    public function findAll(): array
    {
        return $this->model->all()->map(fn($r) => $this->toEntity($r))->toArray();
    }

    public function findByInstructorId(int $instructorId): array
    {
        return $this->model->where('instructor_id', $instructorId)
            ->get()
            ->map(fn($r) => $this->toEntity($r))
            ->toArray();
    }

    public function findDefaultByInstructorId(int $instructorId): ?InstructorVoice
    {
        $record = $this->model
            ->where('instructor_id', $instructorId)
            ->where('is_default', true)
            ->first();
        
        return $record ? $this->toEntity($record) : null;
    }

    public function findByVoiceId(string $voiceId): ?InstructorVoice
    {
        $record = $this->model->where('voice_id', $voiceId)->first();
        return $record ? $this->toEntity($record) : null;
    }

    public function save(InstructorVoice $voice): void
    {
        // If setting as default, unset others first
        if ($voice->isDefault() && !$voice->getId()) {
            $this->unsetDefaultForInstructor($voice->getInstructorId());
        }

        $data = [
            'instructor_id' => $voice->getInstructorId(),
            'voice_id' => $voice->getVoiceId(),
            'name' => $voice->getName(),
            'description' => $voice->getDescription(),
            'settings' => $voice->getSettings()->toArray(),
            'sample_url' => $voice->getSampleUrl(),
            'is_default' => $voice->isDefault(),
        ];

        if ($voice->getId()) {
            $this->model->where('id', $voice->getId())->update($data);
        } else {
            $this->model->create($data);
        }
    }

    public function delete(int $id): void
    {
        $this->model->destroy($id);
    }

    public function unsetDefaultForInstructor(int $instructorId): void
    {
        $this->model
            ->where('instructor_id', $instructorId)
            ->where('is_default', true)
            ->update(['is_default' => false]);
    }

    private function toEntity(InstructorVoiceModel $model): InstructorVoice
    {
        return new InstructorVoice(
            id: $model->id,
            instructorId: $model->instructor_id,
            voiceId: $model->voice_id,
            name: $model->name,
            description: $model->description,
            settings: VoiceSettings::fromArray($model->settings ?? []),
            sampleUrl: $model->sample_url,
            isDefault: $model->is_default,
            createdAt: $model->created_at->toDateTimeImmutable(),
            updatedAt: $model->updated_at->toDateTimeImmutable(),
        );
    }
}
