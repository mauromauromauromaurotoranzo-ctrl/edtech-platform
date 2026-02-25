<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KnowledgeBaseModel extends Model
{
    use HasFactory;

    protected $table = 'knowledge_bases';

    protected $fillable = [
        'instructor_id',
        'title',
        'description',
        'slug',
        'status',
        'public_access',
        'pricing_model',
        'total_chunks',
        'last_indexed_at',
    ];

    protected $casts = [
        'public_access' => 'boolean',
        'total_chunks' => 'integer',
        'last_indexed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(InstructorModel::class, 'instructor_id');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(CourseModel::class, 'knowledge_base_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(SubscriptionModel::class, 'knowledge_base_id');
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(ConversationModel::class, 'knowledge_base_id');
    }
}
