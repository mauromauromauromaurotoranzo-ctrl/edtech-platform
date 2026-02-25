<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserModel extends Model
{
    use HasFactory;

    protected $table = 'users';

    protected $fillable = [
        'email',
        'password_hash',
        'role',
        'name',
        'avatar',
        'bio',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function instructor(): HasOne
    {
        return $this->hasOne(InstructorModel::class, 'user_id');
    }

    public function student(): HasOne
    {
        return $this->hasOne(StudentModel::class, 'user_id');
    }
}
