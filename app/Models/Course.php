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

    /**
     * Determine whether every module in the course has a published, valid quiz.
     */
    public function hasReadyModuleQuizzes(): bool
    {
        $modules = $this->modules()
            ->with('quiz.questions.choices')
            ->get();

        if ($modules->isEmpty()) {
            return false;
        }

        return $modules->every(fn (Module $module): bool => $this->isModuleQuizReady($module));
    }

    /**
     * Return modules whose quiz is missing, unpublished, or has incomplete questions.
     *
     * @return \Illuminate\Support\Collection<int, Module>
     */
    public function incompleteQuizModules()
    {
        return $this->modules()
            ->with('quiz.questions.choices')
            ->get()
            ->reject(fn (Module $module): bool => $this->isModuleQuizReady($module))
            ->values();
    }

    public function isModuleQuizReady(Module $module): bool
    {
        $quiz = $module->quiz;

        return (bool) ($quiz
            && $quiz->is_published
            && $quiz->questions->isNotEmpty()
            && $quiz->questions->every(function ($question): bool {
                if ($question->question_type === 'fill_blank') {
                    return $question->choices->where('is_correct', true)->isNotEmpty();
                }

                return $question->choices->count() >= 2
                    && $question->choices->where('is_correct', true)->count() === 1;
            }));
    }
}
