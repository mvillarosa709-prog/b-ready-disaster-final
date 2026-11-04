<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopTeacher;

class WorkshopPolicy
{
    /**
     * Determine whether the teacher can view the workshop details.
     */
    public function view(User $user, Workshop $workshop): bool
    {
        if ($user->role !== 'teacher') {
            return false;
        }

        if (! $workshop->courseIsPublished()) {
            return false;
        }

        // Available if published or if the teacher is already enrolled
        if (in_array($workshop->status, ['published', 'ongoing'])) {
            return true;
        }

        return WorkshopTeacher::where('workshop_id', $workshop->id)
            ->where('teacher_id', $user->id)
            ->exists();
    }

    /**
     * Determine whether the teacher can join/enroll in the workshop.
     */
    public function join(User $user, Workshop $workshop): bool
    {
        if ($user->role !== 'teacher') {
            return false;
        }

        if (! $workshop->courseIsPublished()) {
            return false;
        }

        // Workshop must be published or ongoing
        if (! in_array($workshop->status, ['published', 'ongoing'])) {
            return false;
        }

        // Check registration deadline if set
        if ($workshop->registration_deadline && now()->isAfter($workshop->registration_deadline)) {
            return false;
        }

        // Must not already be registered or completed
        $enrollment = WorkshopTeacher::where('workshop_id', $workshop->id)
            ->where('teacher_id', $user->id)
            ->first();

        if ($enrollment && in_array($enrollment->status, ['registered', 'completed'])) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the teacher has access to learning materials & modules.
     */
    public function accessLearning(User $user, Workshop $workshop): bool
    {
        if ($user->role !== 'teacher') {
            return false;
        }

        return WorkshopTeacher::where('workshop_id', $workshop->id)
            ->where('teacher_id', $user->id)
            ->whereIn('status', ['registered', 'completed'])
            ->exists();
    }
}
