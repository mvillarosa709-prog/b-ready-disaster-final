<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Certification;
use App\Models\Course;
use App\Models\Workshop;

class TrainerController extends Controller
{
    /**
     * Display the Trainer Dashboard.
     */
    public function dashboard()
    {
        $trainerIds = auth()->user() ? auth()->user()->accessibleTrainerIds() : [auth()->id()];

        // Get courses created by the logged-in trainer.
        $courses = Course::forTrainer()
            ->with('modules')
            ->latest()
            ->get();

        // Get workshops created by the logged-in trainer.
        $workshops = Workshop::forTrainer()
            ->with('course')
            ->withCount('workshopTeachers')
            ->latest('start_date')
            ->take(5)
            ->get();

        $workshopCount = Workshop::forTrainer()->count();
        $publishedWorkshopCount = Workshop::forTrainer()
            ->where('status', 'published')
            ->count();

        $pendingCertificatesCount = Certification::where('status', 'pending')
            ->whereHas('workshop', fn ($query) => $query->whereIn('trainer_id', $trainerIds))
            ->count();

        // Count total courses.
        $courseCount = $courses->count();

        // Count total modules under the trainer's courses.
        $moduleCount = $courses->sum(function ($course) {
            return $course->modules->count();
        });

        // Count published courses.
        $publishedCount = $courses
            ->where('status', 'published')
            ->count();

        $quizCount = Assessment::whereNotNull('module_id')
            ->whereHas('module.course', fn ($query) => $query->whereIn('trainer_id', $trainerIds))
            ->count();
        $submissionCount = AssessmentAttempt::whereHas('assessment.module.course', fn ($query) => $query->whereIn('trainer_id', $trainerIds))
            ->count();

        // Count draft courses.
        $draftCount = $courses
            ->where('status', 'draft')
            ->count();

        return view('trainer.dashboard', compact(
            'courses',
            'workshops',
            'workshopCount',
            'publishedWorkshopCount',
            'pendingCertificatesCount',
            'courseCount',
            'moduleCount',
            'quizCount',
            'submissionCount',
            'publishedCount',
            'draftCount'
        ));
    }
}
