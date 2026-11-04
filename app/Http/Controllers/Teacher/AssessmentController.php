<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreAssessmentAttemptRequest;
use App\Models\AssessmentAttempt;
use App\Models\Certification;
use App\Models\Module;
use App\Models\ModuleProgress;
use App\Models\Workshop;
use App\Services\AssessmentEvaluationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    public function __construct(
        protected AssessmentEvaluationService $evaluationService
    ) {}

    public function submitModule(
        StoreAssessmentAttemptRequest $request,
        Workshop $workshop,
        Module $module
    ): RedirectResponse {
        $teacher = $request->user();
        Gate::authorize('view', [$module, $workshop]);

        $assessment = $workshop->assessments()
            ->where('module_id', $module->id)
            ->first()
            ?? $module->quiz;
        if (! $assessment) {
            abort(404, 'No quiz is assigned to this module.');
        }

        Gate::authorize('attempt', [$assessment, $workshop]);
        $assessment->load('questions.choices');

        $result = $this->evaluationService->evaluateAttempt(
            $teacher,
            $assessment,
            $request->validated('answers'),
            $workshop
        );

        $passed = $result['passed'];
        $progress = ModuleProgress::firstOrNew([
            'workshop_id' => $workshop->id,
            'module_id' => $module->id,
            'teacher_id' => $teacher->id,
        ]);
        $progress->user_id = $teacher->id;
        $progress->progress = $passed ? 100 : max(75, (int) $progress->progress);
        $progress->status = $passed ? 'completed' : 'in_progress';
        $progress->completed_at = $passed ? now() : null;
        $progress->save();

        $resultForView = [
            'module_id' => $module->id,
            'passed' => $passed,
            'percentage' => $result['percentage'],
            'score' => $result['score'],
            'total_points' => $result['total_points'],
            'passing_score' => $assessment->passing_score,
            'attempt_number' => $result['attempt']->attempt_number,
            'attempts_left' => $result['remaining_attempts'],
        ];

        if ($passed) {
            $modules = $workshop->ordered_modules->sortBy('sequence')->values();
            $moduleIndex = $modules->search(fn ($item) => (int) $item->id === (int) $module->id);
            $nextModule = $moduleIndex !== false ? $modules->get($moduleIndex + 1) : null;

            if ($nextModule) {
                $resultForView['next_module_title'] = $nextModule->title;

                ModuleProgress::firstOrCreate(
                    [
                        'workshop_id' => $workshop->id,
                        'module_id' => $nextModule->id,
                        'teacher_id' => $teacher->id,
                    ],
                    [
                        'user_id' => $teacher->id,
                        'progress' => 0,
                        'status' => 'not_started',
                    ]
                );

                return redirect()
                    ->route('teacher.learning.module', [$workshop, $nextModule])
                    ->with('module_quiz_result', $resultForView);
            }
        }

        return redirect()
            ->route('teacher.learning.module', [$workshop, $module])
            ->with('module_quiz_result', $resultForView);
    }

    /**
     * Display instant assessment results with score feedback.
     */
    public function result(Request $request, Workshop $workshop, AssessmentAttempt $attempt): View
    {
        $teacher = $request->user();

        $attemptTeacherId = $attempt->teacher_id ?? $attempt->user_id;
        if ((int) $attemptTeacherId !== (int) $teacher->id || (int) $attempt->workshop_id !== (int) $workshop->id) {
            abort(403, 'Unauthorized attempt record.');
        }

        $attempt->load('assessment');
        $certification = Certification::where(function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
        })
            ->where('workshop_id', $workshop->id)
            ->first();

        $attemptsLeft = max(0, $attempt->assessment->max_attempts - $attempt->attempt_number);

        return view('teacher.assessments.result', compact(
            'workshop',
            'attempt',
            'certification',
            'attemptsLeft'
        ));
    }

    /**
     * Render the printable, high-resolution digital certificate view.
     */
    public function certificate(Request $request, Workshop $workshop): View
    {
        $teacher = $request->user();

        $certification = Certification::where(function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
        })
            ->where('workshop_id', $workshop->id)
            ->with(['workshop.trainer', 'assessmentAttempt', 'approver'])
            ->firstOrFail();

        return view('teacher.assessments.certificate', compact('workshop', 'certification', 'teacher'));
    }
}
