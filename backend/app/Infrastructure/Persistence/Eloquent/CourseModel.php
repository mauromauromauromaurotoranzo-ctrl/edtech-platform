<?php

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseModel extends Model
{
    use SoftDeletes;

    protected $table = 'courses';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'knowledge_base_id',
        'title',
        'description',
        'level',
        'modules',
        'settings',
    ];

    protected $casts = [
        'modules' => 'array',
        'settings' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function knowledgeBase()
    {
        return $this->belongsTo(KnowledgeBaseModel::class, 'knowledge_base_id');
    }
}
