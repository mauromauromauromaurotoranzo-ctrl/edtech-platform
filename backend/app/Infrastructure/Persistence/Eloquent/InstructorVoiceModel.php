<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructorVoiceModel extends Model
{
    use HasFactory;

    protected $table = 'instructor_voices';

    protected $fillable = [
        'instructor_id',
        'voice_id',
        'name',
        'description',
        'settings',
        'sample_url',
        'is_default',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_default' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(InstructorModel::class, 'instructor_id');
    }
}
