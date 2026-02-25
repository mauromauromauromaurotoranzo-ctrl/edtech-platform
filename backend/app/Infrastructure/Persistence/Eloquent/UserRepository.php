<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Entities\User;
use App\Domain\Entities\UserRole;
use App\Domain\RepositoryInterfaces\UserRepositoryInterface;
use App\Domain\ValueObjects\Email;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        private UserModel $model
    ) {}

    public function findById(int $id): ?User
    {
        $record = $this->model->find($id);
        return $record ? $this->toEntity($record) : null;
    }

    public function findByEmail(Email $email): ?User
    {
        $record = $this->model->where('email', $email->getValue())->first();
        return $record ? $this->toEntity($record) : null;
    }

    public function findAll(): array
    {
        return $this->model->all()->map(fn($r) => $this->toEntity($r))->toArray();
    }

    public function findByRole(string $role): array
    {
        return $this->model->where('role', $role)
            ->get()
            ->map(fn($r) => $this->toEntity($r))
            ->toArray();
    }

    public function save(User $user): void
    {
        $data = [
            'email' => $user->getEmail()->getValue(),
            'password_hash' => $user->getPasswordHash(),
            'role' => $user->getRole()->value,
            'name' => $user->getName(),
            'avatar' => $user->getAvatar(),
            'bio' => $user->getBio(),
        ];

        if ($user->getId()) {
            $this->model->where('id', $user->getId())->update($data);
        } else {
            $this->model->create($data);
        }
    }

    public function delete(int $id): void
    {
        $this->model->destroy($id);
    }

    public function existsByEmail(Email $email): bool
    {
        return $this->model->where('email', $email->getValue())->exists();
    }

    private function toEntity(UserModel $model): User
    {
        return new User(
            id: $model->id,
            email: new Email($model->email),
            passwordHash: $model->password_hash,
            role: UserRole::from($model->role),
            name: $model->name,
            avatar: $model->avatar,
            bio: $model->bio,
            createdAt: $model->created_at->toDateTimeImmutable(),
            updatedAt: $model->updated_at->toDateTimeImmutable(),
        );
    }
}
