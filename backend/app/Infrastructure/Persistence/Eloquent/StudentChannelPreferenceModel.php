<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentChannelPreferenceModel extends Model
{
    use HasFactory;

    protected $table = 'student_channel_preferences';

    protected $fillable = [
        'student_id',
        'priority_order',
        'whatsapp_number',
        'telegram_chat_id',
        'email_address',
        'notifications_enabled',
    ];

    protected $casts = [
        'priority_order' => 'array',
        'notifications_enabled' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(StudentModel::class, 'student_id');
    }
}
