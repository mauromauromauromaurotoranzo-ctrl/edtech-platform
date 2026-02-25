<?php

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

class SubscriptionModel extends Model
{
    protected $table = 'subscriptions';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'student_id',
        'knowledge_base_id',
        'status',
        'amount',
        'currency',
        'interval',
        'current_period_starts_at',
        'current_period_ends_at',
        'payment_provider_subscription_id',
        'cancelled_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'current_period_starts_at' => 'datetime',
        'current_period_ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(StudentModel::class, 'student_id');
    }

    public function knowledgeBase()
    {
        return $this->belongsTo(KnowledgeBaseModel::class, 'knowledge_base_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->where('current_period_ends_at', '<=', now()->addDays($days))
                     ->where('status', 'active');
    }
}
