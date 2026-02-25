<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstructorModel extends Model
{
    use HasFactory;

    protected $table = 'instructors';

    protected $fillable = [
        'user_id',
        'expertise_areas',
        'verification_status',
        'stripe_account_id',
    ];

    protected $casts = [
        'expertise_areas' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    public function knowledgeBases(): HasMany
    {
        return $this->hasMany(KnowledgeBaseModel::class, 'instructor_id');
    }
}
