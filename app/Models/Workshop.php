<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'trainer_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'registration_deadline',
        'status',
        'assessment_id',
        'invite_link',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'registration_deadline' => 'datetime',
        ];
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function courseIsPublished(): bool
    {
        return $this->course()->published()->exists();
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function teachers()
    {
        return $this->belongsToMany(
            User::class, 
            'workshop_teachers',
            'workshop_id',
            'teacher_id'
        )->withPivot('status', 'joined_at');
    }

    public function workshopTeachers()
    {
        return $this->hasMany(
            WorkshopTeacher::class,
            'workshop_id'
        );
    }

    public function workshopModules()
    {
        return $this->hasMany(
            WorkshopModule::class,
            'workshop_id'
        )->orderBy('sequence');
    }

    public function modules()
    {
        return $this->belongsToMany(
            Module::class,
            'workshop_modules',
            'workshop_id',
            'module_id'
        )->withPivot('sequence')->orderBy('workshop_modules.sequence');
    }

    /**
     * Retrieve ordered modules for this workshop, preferring workshop_modules sequence or course modules.
     */
    public function getOrderedModulesAttribute()
    {
        if ($this->workshopModules()->exists()) {
            return $this->modules;
        }

        return $this->course ? $this->course->modules->sortBy('sequence')->values() : collect();
    }

    public function assessment()
    {
        return $this->hasOne(
            Assessment::class,
            'workshop_id'
        )->latestOfMany();
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class, 'workshop_id');
    }

    public function classroomPackage()
    {
        return $this->hasOne(
            ClassroomPackage::class,
            'workshop_id'
        );
    }

    public function certifications()
    {
        return $this->hasMany(
            Certification::class,
            'workshop_id'
        );
    }

    public function implementations()
    {
        return $this->hasMany(
            ClassroomImplementation::class,
            'workshop_id'
        );
    }

    public function moduleProgress()
    {
        return $this->hasMany(
            ModuleProgress::class,
            'workshop_id'
        );
    }
}
