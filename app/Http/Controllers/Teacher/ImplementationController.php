<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreImplementationRequest;
use App\Models\Certification;
use App\Models\ClassroomImplementation;
use App\Models\Workshop;
use App\Services\ClassroomImplementationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ImplementationController extends Controller
{
    public function __construct(
        protected ClassroomImplementationService $implementationService
    ) {}

    /**
     * Display a listing of all classroom implementations submitted by the teacher.
     */
    public function index(Request $request): View
    {
        $teacher = $request->user();

        $implementations = ClassroomImplementation::where('teacher_id', $teacher->id)
            ->with(['workshop', 'classroomPackage', 'studentResults'])
            ->latest('implementation_date')
            ->paginate(10);

        // Overall stats across all implementations
        $allResults = ClassroomImplementation::where('teacher_id', $teacher->id)->get();
        $totalSessions = $allResults->count();
        $totalStudents = $allResults->sum('students_participated');
        $totalPassed = $allResults->sum('students_passed');
        $totalCompletedAssessment = $allResults->sum('students_completed_assessment');

        $overallPassRate = $totalCompletedAssessment > 0
            ? round(($totalPassed / $totalCompletedAssessment) * 100, 1)
            : 0;

        return view('teacher.implementation.index', compact(
            'implementations',
            'totalSessions',
            'totalStudents',
            'totalPassed',
            'overallPassRate'
        ));
    }

    /**
     * Show the form for creating a new classroom implementation.
     */
    public function create(Request $request): View
    {
        $teacher = $request->user();

        // Get workshops where teacher is officially certified
        $certifiedWorkshopIds = Certification::where('teacher_id', $teacher->id)
            ->where('status', 'approved')
            ->pluck('workshop_id');

        $eligibleWorkshops = Workshop::whereIn('id', $certifiedWorkshopIds)
            ->whereHas('classroomPackage')
            ->with('classroomPackage')
            ->get();

        if ($eligibleWorkshops->isEmpty()) {
            return view('teacher.implementation.create', [
                'eligibleWorkshops' => collect(),
                'selectedWorkshop' => null,
                'noCertifications' => true,
            ]);
        }

        $selectedWorkshopId = $request->query('workshop_id', $eligibleWorkshops->first()->id);
        $selectedWorkshop = $eligibleWorkshops->firstWhere('id', (int) $selectedWorkshopId) ?? $eligibleWorkshops->first();

        return view('teacher.implementation.create', [
            'eligibleWorkshops' => $eligibleWorkshops,
            'selectedWorkshop' => $selectedWorkshop,
            'noCertifications' => false,
        ]);
    }

    /**
     * Store a newly created classroom implementation in storage.
     */
    public function store(StoreImplementationRequest $request): RedirectResponse
    {
        $teacher = $request->user();
        $workshop = Workshop::findOrFail($request->validated('workshop_id'));

        Gate::authorize('create', [ClassroomImplementation::class, $workshop]);

        $data = $request->validated();
        if ($request->hasFile('supporting_record')) {
            $data['supporting_record'] = $request->file('supporting_record');
        }

        $implementation = $this->implementationService->recordImplementation($teacher, $data);

        return redirect()->route('teacher.implementations.show', $implementation)
            ->with('success', 'Classroom implementation report and student scores successfully recorded!');
    }

    /**
     * Display the specified classroom implementation and its dynamic analytics dashboard.
     */
    public function show(Request $request, ClassroomImplementation $implementation): View
    {
        Gate::authorize('view', $implementation);

        $implementation->load(['workshop.trainer', 'classroomPackage', 'studentResults']);
        $analytics = $this->implementationService->getAnalyticsSummary($implementation);

        return view('teacher.implementation.show', compact('implementation', 'analytics'));
    }
}
