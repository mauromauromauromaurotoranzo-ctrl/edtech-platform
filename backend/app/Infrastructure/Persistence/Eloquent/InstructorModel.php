<?php

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstructorModel extends Model
{
    use SoftDeletes;

    protected $table = 'instructors';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'expertise_areas',
        'bio',
        'voice_clone_id',
        'is_verified',
        'stripe_account_id',
    ];

    protected $casts = [
        'expertise_areas' => 'array',
        'is_verified' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(StudentModel::class, 'user_id');
    }

    public function knowledgeBases()
    {
        return $this->hasMany(KnowledgeBaseModel::class, 'instructor_id');
    }
}
