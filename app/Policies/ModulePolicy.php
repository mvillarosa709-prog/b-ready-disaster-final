<?php

namespace App\Policies;

use App\Models\Module;
use App\Models\ModuleProgress;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopTeacher;

class ModulePolicy
{
    /**
     * Determine whether the teacher can view this specific module in sequential order.
     * Strict Sequencing Rule: If Module N-1 status != 'completed', mark Module N as Locked.
     */
    public function view(User $user, Module $module, Workshop $workshop): bool
    {
        if ($user->role !== 'teacher') {
            return false;
        }

        if (! $workshop->courseIsPublished()) {
            return false;
        }

        // Must be enrolled in workshop
        $isEnrolled = WorkshopTeacher::where('workshop_id', $workshop->id)
            ->where(function ($q) use ($user) {
                $q->where('teacher_id', $user->id)->orWhere('user_id', $user->id);
            })
            ->whereIn('status', ['registered', 'completed'])
            ->exists();

        if (! $isEnrolled) {
            return false;
        }

        // Retrieve ordered sequence of modules for this workshop
        $orderedModules = $workshop->ordered_modules->sortBy('sequence')->values();
        $currentIndex = $orderedModules->search(fn($m) => $m->id === $module->id);

        if ($currentIndex === false) {
            return false;
        }

        // First module in sequence (Index 0) is always unlocked for enrolled teachers
        if ($currentIndex === 0 || $module->sequence <= 1) {
            return true;
        }

        // Strict Sequencing Rule: Module N cannot be accessed if Module N-1 is incomplete
        $precedingModule = $orderedModules->get($currentIndex - 1);
        if (! $precedingModule) {
            return true;
        }

        // Check corresponding row in module_progress
        $precedingProgress = ModuleProgress::where('workshop_id', $workshop->id)
            ->where(function ($q) use ($user) {
                $q->where('teacher_id', $user->id)->orWhere('user_id', $user->id);
            })
            ->where('module_id', $precedingModule->id)
            ->first();

        return $precedingProgress !== null && $precedingProgress->status === 'completed';
    }

    /**
     * Determine whether the teacher can mark this module as completed.
     */
    public function complete(User $user, Module $module, Workshop $workshop): bool
    {
        return $this->view($user, $module, $workshop);
    }
}
