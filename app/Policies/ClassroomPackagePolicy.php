<?php

namespace App\Policies;

use App\Models\Certification;
use App\Models\ClassroomPackage;
use App\Models\User;
use App\Models\WorkshopTeacher;

class ClassroomPackagePolicy
{
    /**
     * Determine whether the teacher can view the package page.
     */
    public function view(User $user, ClassroomPackage $package): bool
    {
        if ($user->role !== 'teacher') {
            return false;
        }

        if (! $package->workshop || ! $package->workshop->courseIsPublished()) {
            return false;
        }

        return WorkshopTeacher::where('workshop_id', $package->workshop_id)
            ->where('teacher_id', $user->id)
            ->whereIn('status', ['registered', 'completed'])
            ->exists();
    }

    /**
     * Determine whether the teacher has unlocked the package (must have certification).
     */
    public function unlock(User $user, ClassroomPackage $package): bool
    {
        if (! $this->view($user, $package)) {
            return false;
        }

        return Certification::where('teacher_id', $user->id)
            ->where('workshop_id', $package->workshop_id)
            ->where('status', 'approved')
            ->exists();
    }

    /**
     * Determine whether the teacher can download materials from this package.
     */
    public function download(User $user, ClassroomPackage $package): bool
    {
        return $this->unlock($user, $package);
    }
}
