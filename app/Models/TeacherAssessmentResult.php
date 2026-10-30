<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherAssessmentResult extends Model
{
    protected $fillable = [
        'teacher_name',
        'score',
        'total_questions',
        'is_passed',
    ];
}