<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'learning_objectives',
        'estimated_duration',
        'sequence',
    ];

    /**
     * The course this module belongs to.
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * Training materials belonging to this module.
     */
    public function materials()
    {
        return $this->hasMany(Material::class, 'module_id')
            ->orderBy('sequence');
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class, 'module_id');
    }

    public function quiz()
    {
        return $this->hasOne(Assessment::class, 'module_id')
            ->whereNull('workshop_id');
    }

    /**
     * Workshop assignments contraining this module.
     */
    public function workshopModules()
    {
        return $this->hasMany(
            WorkshopModule::class,
            'module_id'
        );
    }

    /**
     * Teacher progress records for this module.
     */
    public function progress()
    {
        return $this->hasMany(
            ModuleProgress::class,
            'module_id'
        );
    }
}
