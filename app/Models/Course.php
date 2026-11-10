<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $attributes = [
        'status' => 'draft',
    ];

    protected $fillable = [
        'trainer_id',
        'title',
        'description',
        'learning_objectives',
        'target_participants',
        'estimated_duration',
        'status',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    /**
     * The DRR Trainer who created this course.
     */
    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    /**
     * Modules belonging to this course.
     */
    public function modules()
    {
        return $this->hasMany(Module::class, 'course_id')
            ->orderBy('sequence');
    }

    /**
     * Workshops associated with this course.
     */
    public function workshops()
    {
        return $this->hasMany(Workshop::class, 'course_id');
    }

    public function hasReadyModuleQuizzes(): bool
    {
        $modules = $this->modules()
            ->with('quiz.questions.choices')
            ->get();

        if ($modules->isEmpty()) {
            return false;
        }

        return $modules->every(function (Module $module): bool {
            $quiz = $module->quiz;

            return $quiz
                && $quiz->is_published
                && $quiz->questions->isNotEmpty()
                && $quiz->questions->every(fn ($question) =>
                    $question->choices->count() >= 2
                    && $question->choices->where('is_correct', true)->count() === 1
                );
        });
    }
}
