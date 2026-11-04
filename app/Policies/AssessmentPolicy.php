<?php

namespace App\Policies;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Certification;
use App\Models\ModuleProgress;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopTeacher;

class AssessmentPolicy
{
    /**
     * Determine whether the teacher can view assessment info.
     */
    public function view(User $user, Assessment $assessment, ?Workshop $workshop = null): bool
    {
        if ($user->role !== 'teacher') {
            return false;
        }

        $workshop ??= $assessment->workshop;
        if (! $workshop || ! $workshop->courseIsPublished()) {
            return false;
        }

        if ($assessment->workshop_id && (int) $assessment->workshop_id !== (int) $workshop->id) {
            return false;
        }

        if ($assessment->module_id && (int) $assessment->module?->course_id !== (int) $workshop->course_id) {
            return false;
        }

        return WorkshopTeacher::where('workshop_id', $workshop->id)
            ->where(function ($q) use ($user) {
                $q->where('teacher_id', $user->id)->orWhere('user_id', $user->id);
            })
            ->whereIn('status', ['registered', 'completed'])
            ->exists();
    }

    /**
     * Determine whether the teacher can attempt the assessment.
     * Unlocking Rule: Evaluate if all required modules for the workshop have module_progress.status == 'completed' (100% completion).
     */
    public function attempt(User $user, Assessment $assessment, Workshop $workshop): bool
    {
        if (! $this->view($user, $assessment, $workshop)) {
            return false;
        }

        if (! $assessment->is_published) {
            return false;
        }

        // Check if teacher has already certified/passed
        $alreadyCertified = Certification::where(function ($q) use ($user) {
                $q->where('teacher_id', $user->id)->orWhere('user_id', $user->id);
            })
            ->where('workshop_id', $workshop->id)
            ->exists();

        if ($alreadyCertified) {
            return false;
        }

        // Check max attempts
        $attemptCount = AssessmentAttempt::where('assessment_id', $assessment->id)
            ->where('workshop_id', $workshop->id)
            ->where(function ($q) use ($user) {
                $q->where('teacher_id', $user->id)->orWhere('user_id', $user->id);
            })
            ->count();

        if ($attemptCount >= $assessment->max_attempts) {
            return false;
        }

        $alreadyPassed = AssessmentAttempt::where('assessment_id', $assessment->id)
            ->where('workshop_id', $workshop->id)
            ->where(function ($query) use ($user) {
                $query->where('teacher_id', $user->id)->orWhere('user_id', $user->id);
            })
            ->where('result', 'passed')
            ->exists();

        if ($alreadyPassed) {
            return false;
        }

        if ($assessment->module_id) {
            $modules = $workshop->ordered_modules->sortBy('sequence')->values();
            $moduleIndex = $modules->search(
                fn ($module) => (int) $module->id === (int) $assessment->module_id
            );

            if ($moduleIndex === false) {
                return false;
            }

            $prerequisiteIds = $modules
                ->take($moduleIndex)
                ->where('is_required', true)
                ->pluck('id');

            if ($prerequisiteIds->isNotEmpty()) {
                $completedPrerequisiteCount = ModuleProgress::where('workshop_id', $workshop->id)
                    ->where(function ($query) use ($user) {
                        $query->where('teacher_id', $user->id)->orWhere('user_id', $user->id);
                    })
                    ->whereIn('module_id', $prerequisiteIds)
                    ->where('status', 'completed')
                    ->count();

                if ($completedPrerequisiteCount < $prerequisiteIds->count()) {
                    return false;
                }
            }

            return true;
        }

        // Unlocking Rule: Evaluate if all required modules for the workshop have status == 'completed' (100% completion)
        $modules = $workshop->ordered_modules;
        $requiredModules = $modules->where('is_required', true);

        if ($requiredModules->isEmpty()) {
            return true;
        }

        $completedCount = ModuleProgress::where('workshop_id', $workshop->id)
            ->where(function ($q) use ($user) {
                $q->where('teacher_id', $user->id)->orWhere('user_id', $user->id);
            })
            ->whereIn('module_id', $requiredModules->pluck('id'))
            ->where('status', 'completed')
            ->count();

        return $completedCount >= $requiredModules->count();
    }
}
