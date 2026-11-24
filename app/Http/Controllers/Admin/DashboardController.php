<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use App\Models\ClassroomImplementation;
use App\Models\Course;
use App\Models\TeacherAssessmentResult;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Super Admin overview dashboard.
     */
    public function index(Request $request): View
    {
        $totalUsers = User::count();
        $adminCount = User::where('role', 'admin')->count();
        $trainerCount = User::where('role', 'trainer')->count();
        $teacherCount = User::where('role', 'teacher')->count();

        $totalCourses = Course::count();
        $publishedCourses = Course::where('status', 'published')->count();

        $totalWorkshops = Workshop::count();
        $publishedWorkshops = Workshop::where('status', 'published')->count();

        $pendingCertificates = Certification::where('status', 'pending')->count();
        $approvedCertificates = Certification::where('status', 'approved')->count();

        $totalImplementations = ClassroomImplementation::count();

        $recentUsers = User::latest()->take(6)->get();
        $recentWorkshops = Workshop::with(['trainer', 'course'])->latest()->take(4)->get();

        $assessmentResults = TeacherAssessmentResult::all();
        $passedAssessments = $assessmentResults->where('is_passed', true)->count();
        $overallPassRate = $assessmentResults->count() > 0
            ? round(($passedAssessments / $assessmentResults->count()) * 100, 1)
            : 0;

        return view('admin.dashboard', compact(
            'totalUsers',
            'adminCount',
            'trainerCount',
            'teacherCount',
            'totalCourses',
            'publishedCourses',
            'totalWorkshops',
            'publishedWorkshops',
            'pendingCertificates',
            'approvedCertificates',
            'totalImplementations',
            'recentUsers',
            'recentWorkshops',
            'overallPassRate'
        ));
    }
}
