<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationModel extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'student_id',
        'subject',
        'content',
        'channel',
        'status',
        'external_id',
        'sent_at',
        'delivered_at',
        'read_at',
        'error_message',
        'retry_count',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
        'retry_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(StudentModel::class, 'student_id');
    }
}
