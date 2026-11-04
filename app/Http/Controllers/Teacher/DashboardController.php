<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AssessmentAttempt;
use App\Models\Certification;
use App\Models\ClassroomImplementation;
use App\Models\ModuleProgress;
use App\Models\StudentResult;
use App\Models\Workshop;
use App\Models\WorkshopTeacher;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the teacher's main dashboard with comprehensive personal reports aggregation.
     */
    public function index(Request $request): View
    {
        $teacher = $request->user();

        // Enrolled workshops
        $enrollments = WorkshopTeacher::where(function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
        })
            ->whereHas('workshop.course', fn ($query) => $query->published())
            ->with(['workshop.course.modules', 'workshop.trainer', 'workshop.classroomPackage'])
            ->get();

        $enrolledWorkshopIds = $enrollments->pluck('workshop_id');

        // Detailed stats
        $totalEnrolled = $enrollments->count();
        $totalCompleted = $enrollments->where('status', 'completed')->count();
        $totalInProgress = $totalEnrolled - $totalCompleted;

        // Total certifications
        $certifications = Certification::where(function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
        })
            ->with(['workshop.trainer', 'workshop.classroomPackage', 'assessmentAttempt'])
            ->latest('certified_at')
            ->get();
        $totalCertifications = $certifications->count();

        // Modules completed count
        $completedModulesCount = ModuleProgress::where(function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
        })
            ->where('status', 'completed')
            ->count();

        // Implementations & Students Reached
        $implementations = ClassroomImplementation::where(function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
        })
            ->with(['workshop', 'classroomPackage', 'studentResults'])
            ->latest('implementation_date')
            ->get();

        $totalImplementations = $implementations->count();
        $totalStudentsReached = $implementations->sum('students_participated');

        // Dynamic Class Average across all teacher implementations
        $implementationIds = $implementations->pluck('id')->toArray();
        $overallClassAverage = ! empty($implementationIds)
            ? round((float) StudentResult::whereIn('classroom_implementation_id', $implementationIds)->avg('percentage'), 2)
            : 0.0;

        // 1. My Training Progress: Workshops joined, modules completed, and progress percentages
        $activeWorkshops = $enrollments->map(function ($enrollment) use ($teacher) {
            $workshop = $enrollment->workshop;
            if (! $workshop) {
                return null;
            }

            $modules = $workshop->ordered_modules;
            $totalModules = $modules->count();

            $completedModuleIds = ModuleProgress::where('workshop_id', $workshop->id)
                ->where(function ($q) use ($teacher) {
                    $q->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
                })
                ->where('status', 'completed')
                ->pluck('module_id')
                ->toArray();

            $completedCount = count($completedModuleIds);
            $percentage = $totalModules > 0 ? round(($completedCount / $totalModules) * 100) : 0;

            // Find next module to work on
            $nextModule = $modules->first(function ($mod) use ($completedModuleIds) {
                return ! in_array($mod->id, $completedModuleIds);
            });

            // Check certification
            $hasCertificate = Certification::where(function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
            })
                ->where('workshop_id', $workshop->id)
                ->where('status', 'approved')
                ->exists();

            return (object) [
                'workshop' => $workshop,
                'enrollment' => $enrollment,
                'total_modules' => $totalModules,
                'completed_modules' => $completedCount,
                'progress_percentage' => $percentage,
                'next_module' => $nextModule,
                'has_certificate' => $hasCertificate,
                'is_completed' => $enrollment->status === 'completed' || $hasCertificate,
            ];
        })->filter()->values();

        // 2. My Assessment: Recent attempt logs, percentages, and final status
        $recentAttempts = AssessmentAttempt::where(function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
        })
            ->with(['assessment.workshop', 'workshop'])
            ->latest('submitted_at')
            ->take(5)
            ->get();

        // Available upcoming published workshops (not yet enrolled & registration deadline open)
        $availableWorkshops = Workshop::whereIn('status', ['published', 'ongoing'])
            ->whereHas('course', fn ($query) => $query->published())
            ->whereNotIn('id', $enrolledWorkshopIds)
            ->where(function ($q) {
                $q->whereNull('registration_deadline')
                    ->orWhere('registration_deadline', '>=', now());
            })
            ->with(['trainer', 'course'])
            ->latest('start_date')
            ->take(3)
            ->get();

        return view('teacher.dashboard', compact(
            'totalEnrolled',
            'totalCompleted',
            'totalInProgress',
            'totalCertifications',
            'completedModulesCount',
            'totalImplementations',
            'totalStudentsReached',
            'overallClassAverage',
            'activeWorkshops',
            'recentAttempts',
            'certifications',
            'implementations',
            'availableWorkshops'
        ));
    }
}
