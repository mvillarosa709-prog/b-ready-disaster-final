<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreAssessmentAttemptRequest;
use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Certification;
use App\Models\Choice;
use App\Models\Module;
use App\Models\ModuleProgress;
use App\Models\Question;
use App\Models\Workshop;
use App\Models\WorkshopTeacher;
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

        $assessment = $module->quiz
            ?? $workshop->assessments()
                ->where('module_id', $module->id)
                ->first();
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

            // Last module passed -> redirect to Final Exam or Workshop Overview
            return redirect()
                ->route('teacher.workshops.show', $workshop)
                ->with('success', 'All sequential modules completed! You have unlocked the Final Examination. Pass the Final Exam below to unlock the Workshop Repository.');
        }

        return redirect()
            ->route('teacher.learning.module', [$workshop, $module])
            ->with('module_quiz_result', $resultForView);
    }

    /**
     * Show the Workshop Final Examination page (unlocked once all required modules are completed).
     */
    public function showFinalExam(Request $request, Workshop $workshop): View|RedirectResponse
    {
        $teacher = $request->user();

        $isEnrolled = WorkshopTeacher::where('workshop_id', $workshop->id)
            ->where(function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
            })
            ->whereIn('status', ['registered', 'completed'])
            ->exists();

        if (! $isEnrolled) {
            return redirect()->route('teacher.workshops.show', $workshop)
                ->with('warning', 'You must register for this workshop first.');
        }

        $modules = $workshop->ordered_modules->sortBy('sequence')->values();
        $requiredModuleIds = $modules->where('is_required', true)->pluck('id')->toArray();

        $completedModuleIds = ModuleProgress::where('workshop_id', $workshop->id)
            ->where(function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
            })
            ->where('status', 'completed')
            ->pluck('module_id')
            ->toArray();

        $allRequiredCompleted = empty(array_diff($requiredModuleIds, $completedModuleIds));

        if (! $allRequiredCompleted) {
            return redirect()->route('teacher.workshops.show', $workshop)
                ->with('warning', 'Complete all required sequential modules first to unlock the Final Examination.');
        }

        $assessment = $this->ensureFinalExam($workshop);
        $assessment->load('questions.choices');

        $attempts = AssessmentAttempt::where('assessment_id', $assessment->id)
            ->where('workshop_id', $workshop->id)
            ->where(function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
            })
            ->orderByDesc('attempt_number')
            ->get();

        $latestAttempt = $attempts->first();
        $attemptsLeft = max(0, $assessment->max_attempts - $attempts->count());

        $certification = Certification::where(function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
        })
            ->where('workshop_id', $workshop->id)
            ->first();

        return view('teacher.assessments.final-exam', compact(
            'workshop',
            'assessment',
            'latestAttempt',
            'attemptsLeft',
            'certification'
        ));
    }

    /**
     * Submit and evaluate the Workshop Final Examination.
     * Passing the Final Exam issues an approved certification and unlocks the Workshop Repository.
     */
    public function submitFinalExam(
        StoreAssessmentAttemptRequest $request,
        Workshop $workshop
    ): RedirectResponse {
        $teacher = $request->user();
        $assessment = $this->ensureFinalExam($workshop);

        Gate::authorize('attempt', [$assessment, $workshop]);
        $assessment->load('questions.choices');

        $result = $this->evaluationService->evaluateAttempt(
            $teacher,
            $assessment,
            $request->validated('answers'),
            $workshop
        );

        return redirect()
            ->route('teacher.assessments.result', [$workshop, $result['attempt']])
            ->with(
                $result['passed'] ? 'success' : 'warning',
                $result['passed']
                    ? 'Congratulations! You passed the Final Examination. Your Certificate and Workshop Repository are now unlocked!'
                    : 'You did not reach the passing score on the Final Examination. Please review your result and try again.'
            );
    }

    /**
     * Ensure a workshop-level Final Examination (module_id = null) with questions exists.
     */
    private function ensureFinalExam(Workshop $workshop): Assessment
    {
        $assessment = Assessment::where('workshop_id', $workshop->id)
            ->whereNull('module_id')
            ->first();

        if (! $assessment) {
            $assessment = Assessment::create([
                'workshop_id' => $workshop->id,
                'module_id' => null,
                'trainer_id' => $workshop->trainer_id,
                'title' => 'Final Examination: ' . $workshop->title,
                'description' => 'Comprehensive final competency examination covering all modules in ' . $workshop->title . '. Pass this exam to unlock your certification and the Workshop Repository.',
                'passing_score' => 75,
                'time_limit' => 30,
                'max_attempts' => 3,
                'is_published' => true,
            ]);
        }

        if ($assessment->questions()->count() === 0) {
            $q1 = Question::create([
                'assessment_id' => $assessment->id,
                'question_text' => 'During a school emergency covered in "' . $workshop->title . '", what is the teacher\'s primary responsibility before and during evacuation?',
                'question_type' => 'multiple_choice',
                'points' => 25,
                'sequence' => 1,
            ]);
            Choice::create(['question_id' => $q1->id, 'choice_text' => 'Execute standardized protective actions, maintain calm command of learners, and guide them along verified safe exit routes.', 'is_correct' => true, 'sequence' => 1]);
            Choice::create(['question_id' => $q1->id, 'choice_text' => 'Allow students to leave the campus unassisted without headcount verification.', 'is_correct' => false, 'sequence' => 2]);
            Choice::create(['question_id' => $q1->id, 'choice_text' => 'Wait for external media before initiating classroom safety procedures.', 'is_correct' => false, 'sequence' => 3]);
            Choice::create(['question_id' => $q1->id, 'choice_text' => 'Lock all emergency exits to prevent movement.', 'is_correct' => false, 'sequence' => 4]);

            $q2 = Question::create([
                'assessment_id' => $assessment->id,
                'question_text' => 'Why is a pre-incident classroom hazard audit essential for Disaster Risk Reduction and Management (DRRM)?',
                'question_type' => 'multiple_choice',
                'points' => 25,
                'sequence' => 2,
            ]);
            Choice::create(['question_id' => $q2->id, 'choice_text' => 'It identifies and mitigates non-structural, chemical, electrical, or flood hazards before an emergency occurs.', 'is_correct' => true, 'sequence' => 1]);
            Choice::create(['question_id' => $q2->id, 'choice_text' => 'It replaces the need for conducting school-wide evacuation drills.', 'is_correct' => false, 'sequence' => 2]);
            Choice::create(['question_id' => $q2->id, 'choice_text' => 'It is only used for decorating classroom walls.', 'is_correct' => false, 'sequence' => 3]);
            Choice::create(['question_id' => $q2->id, 'choice_text' => 'It eliminates the need for emergency go-bags and first-aid kits.', 'is_correct' => false, 'sequence' => 4]);

            $q3 = Question::create([
                'assessment_id' => $assessment->id,
                'question_text' => 'Upon arriving at the designated safe assembly zone, what must the teacher immediately perform?',
                'question_type' => 'multiple_choice',
                'points' => 25,
                'sequence' => 3,
            ]);
            Choice::create(['question_id' => $q3->id, 'choice_text' => 'Conduct a complete student headcount/buddy-check and report status to the School Incident Commander.', 'is_correct' => true, 'sequence' => 1]);
            Choice::create(['question_id' => $q3->id, 'choice_text' => 'Send students back inside the building to collect personal belongings.', 'is_correct' => false, 'sequence' => 2]);
            Choice::create(['question_id' => $q3->id, 'choice_text' => 'Dismiss students immediately without guardian verification.', 'is_correct' => false, 'sequence' => 3]);
            Choice::create(['question_id' => $q3->id, 'choice_text' => 'Leave the assembly area unattended.', 'is_correct' => false, 'sequence' => 4]);

            $q4 = Question::create([
                'assessment_id' => $assessment->id,
                'question_text' => 'How should a certified teacher utilize the unlocked Workshop Repository & Classroom Teaching Kit after passing this exam?',
                'question_type' => 'multiple_choice',
                'points' => 25,
                'sequence' => 4,
            ]);
            Choice::create(['question_id' => $q4->id, 'choice_text' => 'Download the teacher guides, student manuals, and worksheets to conduct classroom DRR sessions and log student assessment results.', 'is_correct' => true, 'sequence' => 1]);
            Choice::create(['question_id' => $q4->id, 'choice_text' => 'Keep the teaching materials archived without sharing safety protocols with learners.', 'is_correct' => false, 'sequence' => 2]);
            Choice::create(['question_id' => $q4->id, 'choice_text' => 'Skip classroom drills and only post the certificate online.', 'is_correct' => false, 'sequence' => 3]);
            Choice::create(['question_id' => $q4->id, 'choice_text' => 'Modify emergency warning signals without consulting school DRRM authorities.', 'is_correct' => false, 'sequence' => 4]);
        }

        return $assessment;
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
