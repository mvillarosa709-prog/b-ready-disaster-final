<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'permissions',
        'is_active',
        'id_number',
        'id_file_path',
        'verification_status',
        'must_change_password',
        'email_verified_at',
    ];
    
    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
            'permissions' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Courses created by this DRR Trainer.
     */
    public function courses()
    {
        return $this->hasMany(Course::class, 'trainer_id');
    }

    /**
     * Workshops created by this DRR Trainer.
     */
    public function createdWorkshops()
    {
        return $this->hasMany(Workshop::class, 'trainer_id');
    }

    /**
     * Workshops joined by this Teacher.
     */
    public function workshops()
    {
        return $this->belongsToMany(
            Workshop::class,
            'workshop_teachers',
            'teacher_id',
            'workshop_id'
        )->withPivot('status', 'joined_at');
    }

    /**
     * Module progress recods for this Teacher.
     */
    public function moduleProgress()
    {
        return $this->hasMany(ModuleProgress::class, 'teacher_id');
    }

    /**
     * Assessment attempts made by this Teacher.
     */
    public function assessmentAttempts()
    {
        return $this->hasMany(AssessmentAttempt::class, 'teacher_id');
    }
    
    /**
     * Classroom implementations conducted by this Teacher.
     */
    public function classroomImplementations()
    {
        return $this->hasMany(
            ClassroomImplementation::class,
            'teacher_id'
        );
    }

    /**
     * DRR Certifications awarded to this Teacher.
     */
    public function certifications()
    {
        return $this->hasMany(Certification::class, 'teacher_id');
    }

    /**
     * Check if user is a trainer.
     */
    public function isTrainer(): bool
    {
        return $this->role === 'trainer';
    }

    /**
     * Check if user is a teacher.
     */
    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    /**
     * Check if user is an admin / super admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Complete catalogue of configurable permissions in B-READY.
     */
    public static function allAvailablePermissions(): array
    {
        return [
            'workshops.manage' => [
                'label' => 'Manage Workshops',
                'description' => 'Create, edit, publish, and delete workshops and schedules',
                'category' => 'Workshops',
            ],
            'courses.manage' => [
                'label' => 'Manage Courses & Modules',
                'description' => 'Author courses, modules, materials, and module quizzes',
                'category' => 'Courses & Modules',
            ],
            'certificates.manage' => [
                'label' => 'Approve Certificates',
                'description' => 'Review, approve, and digitally sign teacher certifications',
                'category' => 'Certifications',
            ],
            'packages.access' => [
                'label' => 'Classroom Packages Access',
                'description' => 'View, preview, and download student disaster preparedness classroom kits',
                'category' => 'Classroom Packages',
            ],
            'implementations.manage' => [
                'label' => 'Classroom Implementations',
                'description' => 'Submit and log disaster drill implementations and student results',
                'category' => 'Implementations',
            ],
            'reports.view' => [
                'label' => 'View Analytics & Reports',
                'description' => 'Access assessment scores, progress statistics, and institutional reports',
                'category' => 'Reports & Analytics',
            ],
            'users.manage' => [
                'label' => 'User & Role Administration',
                'description' => 'Create users, modify roles, and grant/revoke module permissions',
                'category' => 'System Administration',
            ],
        ];
    }

    /**
     * Default permissions granted to a role when no custom permissions override is set.
     */
    public static function defaultPermissionsForRole(?string $role): array
    {
        return match ($role) {
            'admin' => array_keys(self::allAvailablePermissions()),
            'trainer' => ['workshops.manage', 'courses.manage', 'certificates.manage', 'reports.view'],
            'teacher' => ['packages.access', 'implementations.manage', 'reports.view'],
            default => [],
        };
    }

    /**
     * Check if the user has a given permission.
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if (is_array($this->permissions)) {
            return in_array($permission, $this->permissions, true);
        }

        return in_array($permission, self::defaultPermissionsForRole($this->role), true);
    }

    /**
     * Get the effective list of permissions for this user.
     */
    public function getEffectivePermissions(): array
    {
        if ($this->isAdmin()) {
            return array_keys(self::allAvailablePermissions());
        }

        return is_array($this->permissions)
            ? $this->permissions
            : self::defaultPermissionsForRole($this->role);
    }
}
