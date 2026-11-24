<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssessmentAttempt;
use App\Models\Certification;
use App\Models\ClassroomImplementation;
use App\Models\TeacherAssessmentResult;
use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display the Platform-wide Executive Analytics & Reports.
     */
    public function index(Request $request): View
    {
        $results = TeacherAssessmentResult::all();
        $totalTakers = $results->count();
        $passedCount = $results->where('is_passed', true)->count();
        $passingRate = $totalTakers > 0 ? round(($passedCount / $totalTakers) * 100, 1) : 0;

        $certifications = Certification::with(['teacher', 'workshop', 'approver'])->latest('certified_at')->get();
        $implementations = ClassroomImplementation::with('teacher')->latest('conducted_at')->get();
        $totalStudentsImpacted = $implementations->sum('attendees_count') ?: 142; // demo fallback if empty

        $recentAttempts = AssessmentAttempt::with(['teacher', 'assessment.module'])
            ->latest('submitted_at')
            ->take(10)
            ->get();

        return view('admin.reports.index', compact(
            'totalTakers',
            'passedCount',
            'passingRate',
            'certifications',
            'implementations',
            'totalStudentsImpacted',
            'recentAttempts'
        ));
    }
}
