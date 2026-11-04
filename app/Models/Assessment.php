<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'workshop_id',
        'module_id',
        'trainer_id',
        'title',
        'description',
        'passing_score',
        'time_limit',
        'max_attempts',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'passing_score' => 'integer',
            'time_limit' => 'integer',
            'max_attempts' => 'integer',
        ];
    }

    public function workshop()
    {
        return $this->belongsTo(
            Workshop::class,
            'workshop_id'
        );
    }

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

    public function trainer()
    {
        return $this->belongsTo(
            User::class,
            'trainer_id'
        );
    }

    public function questions()
    {
        return $this->hasMany(
            Question::class,
            'assessment_id'
        )->orderBy('sequence');
    }

    public function attempts()
    {
        return $this->hasMany(
            AssessmentAttempt::class,
            'assessment_id'
        );
    }
}
