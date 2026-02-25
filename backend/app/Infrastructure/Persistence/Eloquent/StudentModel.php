<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentModel extends Model
{
    use HasFactory;

    protected $table = 'students';

    protected $fillable = [
        'user_id',
        'learning_preferences',
    ];

    protected $casts = [
        'learning_preferences' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(SubscriptionModel::class, 'student_id');
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(ConversationModel::class, 'student_id');
    }
}
