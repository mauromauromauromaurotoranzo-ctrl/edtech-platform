<?php

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentModel extends Model
{
    use SoftDeletes;

    protected $table = 'users';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'email',
        'password',
        'role',
        'name',
        'preferences',
        'email_verified_at',
    ];

    protected $casts = [
        'preferences' => 'array',
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function subscriptions()
    {
        return $this->hasMany(SubscriptionModel::class, 'student_id');
    }

    public function conversations()
    {
        return $this->hasMany(ConversationModel::class, 'student_id');
    }
}
