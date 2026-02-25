<?php

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KnowledgeBaseModel extends Model
{
    use SoftDeletes;

    protected $table = 'knowledge_bases';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'instructor_id',
        'title',
        'description',
        'slug',
        'status',
        'settings',
        'total_chunks',
        'last_indexed_at',
    ];

    protected $casts = [
        'settings' => 'array',
        'total_chunks' => 'integer',
        'last_indexed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function instructor()
    {
        return $this->belongsTo(InstructorModel::class, 'instructor_id');
    }

    public function courses()
    {
        return $this->hasMany(CourseModel::class, 'knowledge_base_id');
    }

    public function contentChunks()
    {
        return $this->hasMany(ContentChunkModel::class, 'knowledge_base_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(SubscriptionModel::class, 'knowledge_base_id');
    }

    public function conversations()
    {
        return $this->hasMany(ConversationModel::class, 'knowledge_base_id');
    }
}
