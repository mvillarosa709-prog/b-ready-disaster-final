<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'workshop_id',
        'teacher_id',
        'attempt_number',
        'score',
        'percentage',
        'result',
        'started_at',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'percentage' => 'decimal:2',
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
        ];
    }

    public function getTotalPointsAttribute(): float
    {
        return (float) ($this->assessment ? $this->assessment->questions->sum('points') : 100);
    }

    public function assessment()
    {
        return $this->belongsTo(
            Assessment::class,
            'assessment_id'
        );
    }

    public function workshop()
    {
        return $this->belongsTo(Workshop::class, 'workshop_id');
    }

    public function teacher()
    {
        return $this->belongsTo(
            User::class,
            'teacher_id'
        );
    }

    public function answers()
    {
        return $this->hasMany(
            AssessmentAnswer::class,
            'attempt_id'
        );
    }

    public function certification()
    {
        return $this->hasOne(
            Certification::class,
            'assessment_attempt_id'
        );
    }
}
