<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Course;

class TrainerController extends Controller
{
    /**
     * Display the Trainer Dashboard.
     */
    public function dashboard()
    {
        $trainerId = auth()->id();

        // Get courses created by the logged-in trainer.
        $courses = Course::where('trainer_id', $trainerId)
            ->with('modules')
            ->latest()
            ->get();

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
            ->whereHas('module.course', fn ($query) => $query->where('trainer_id', $trainerId))
            ->count();
        $submissionCount = AssessmentAttempt::whereHas('assessment.module.course', fn ($query) => $query->where('trainer_id', $trainerId))
            ->count();

        // Count draft courses.
        $draftCount = $courses
            ->where('status', 'draft')
            ->count();

        return view('trainer.dashboard', compact(
            'courses',
            'courseCount',
            'moduleCount',
            'quizCount',
            'submissionCount',
            'publishedCount',
            'draftCount'
        ));
    }
}
