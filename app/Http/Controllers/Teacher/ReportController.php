<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AssessmentAttempt;
use App\Models\Certification;
use App\Models\ClassroomImplementation;
use App\Models\ModuleProgress;
use App\Models\StudentResult;
use App\Models\WorkshopTeacher;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display the comprehensive Teacher Personal Reports Dashboard aggregating:
     * 1. My Training Progress: Workshops joined, modules completed, and progress percentages.
     * 2. My Assessment: Attempt logs, percentages, and final status.
     * 3. My Certificate: Badge view, certificate link, and issuance date.
     * 4. My Classroom Implementation: Past logs, aggregate student reach, computed class averages, and written reflections.
     */
    public function index(Request $request): View
    {
        $teacher = $request->user();

        // 1. My Training Progress
        $enrollments = WorkshopTeacher::where(function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
            })
            ->with(['workshop.trainer', 'workshop.course.modules', 'workshop.assessment', 'workshop.classroomPackage'])
            ->latest('joined_at')
            ->get();

        $trainingProgress = $enrollments->map(function ($enrollment) use ($teacher) {
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

            $nextModule = $modules->first(function ($mod) use ($completedModuleIds) {
                return ! in_array($mod->id, $completedModuleIds);
            });

            $certification = Certification::where(function ($q) use ($teacher) {
                    $q->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
                })
                ->where('workshop_id', $workshop->id)
                ->first();

            return (object) [
                'workshop' => $workshop,
                'enrollment' => $enrollment,
                'total_modules' => $totalModules,
                'completed_modules' => $completedCount,
                'progress_percentage' => $percentage,
                'next_module' => $nextModule,
                'certification' => $certification,
                'status' => $enrollment->status,
                'is_completed' => $enrollment->status === 'completed' || $certification !== null,
            ];
        })->filter()->values();

        // 2. My Assessment: Attempt logs, percentages, and final status
        $assessmentAttempts = AssessmentAttempt::where(function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
            })
            ->with(['assessment.workshop', 'workshop', 'answers'])
            ->latest('submitted_at')
            ->get();

        // 3. My Certificate: Badge view, certificate link, and issuance date
        $certifications = Certification::where(function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
            })
            ->with(['workshop.trainer', 'workshop.classroomPackage', 'assessmentAttempt'])
            ->latest('certified_at')
            ->get();

        // 4. My Classroom Implementation: Past logs, aggregate student reach, computed class averages, and written reflections
        $implementations = ClassroomImplementation::where(function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
            })
            ->with(['workshop', 'classroomPackage', 'studentResults'])
            ->latest('implementation_date')
            ->get();

        $implementationIds = $implementations->pluck('id')->toArray();

        // Automated Summary Calculations (No Manual Input Rule)
        $totalImplementations = $implementations->count();
        $totalStudentsReached = $implementations->sum('students_participated');
        $totalPassedStudents = $implementations->sum('students_passed');
        $totalFailedStudents = $implementations->sum('students_failed');

        // Dynamically compute class average across all students using database AVG aggregation
        $overallClassAverage = ! empty($implementationIds)
            ? round((float) StudentResult::whereIn('classroom_implementation_id', $implementationIds)->avg('percentage'), 2)
            : 0.0;

        return view('teacher.reports.index', compact(
            'trainingProgress',
            'assessmentAttempts',
            'certifications',
            'implementations',
            'totalImplementations',
            'totalStudentsReached',
            'totalPassedStudents',
            'totalFailedStudents',
            'overallClassAverage'
        ));
    }
}
