<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AssessmentAttempt;
use App\Models\Certification;
use App\Models\Material;
use App\Models\Module;
use App\Models\ModuleProgress;
use App\Models\Workshop;
use App\Models\WorkshopTeacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LearningController extends Controller
{
    /**
     * Display the sequential module viewer with material preview and accordion list.
     * Enforces Strict Sequencing Rule: If Module N-1 status != 'completed', mark Module N as Locked.
     */
    public function show(Request $request, Workshop $workshop, Module $module): View|RedirectResponse
    {
        $teacher = $request->user();
        $assessment = $workshop->assessments()
            ->where('module_id', $module->id)
            ->first()
            ?? $module->quiz;

        // Must enroll first to take the course and access modules
        $isEnrolled = WorkshopTeacher::where('workshop_id', $workshop->id)
            ->where(function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
            })
            ->whereIn('status', ['registered', 'completed'])
            ->exists();

        if (! $isEnrolled) {
            return redirect()->route('teacher.workshops.show', $workshop)
                ->with('warning', "You must register for {$workshop->title} first before accessing course modules.");
        }

        Gate::authorize('view', [$module, $workshop]);

        $workshop->load(['trainer', 'course.modules.materials']);
        $allModules = $workshop->ordered_modules->sortBy('sequence')->values();

        // Progress records for all modules in this workshop
        $progressList = ModuleProgress::where('workshop_id', $workshop->id)
            ->where(function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
            })
            ->get()
            ->keyBy('module_id');

        $completedModuleIds = $progressList->where('status', 'completed')->pluck('module_id')->toArray();

        // Mark current module in_progress if not already started
        $currentProgress = ModuleProgress::firstOrCreate(
            [
                'workshop_id' => $workshop->id,
                'module_id' => $module->id,
                'teacher_id' => $teacher->id,
            ],
            [
                'user_id' => $teacher->id,
                'progress' => 25,
                'status' => 'in_progress',
                'started_at' => now(),
            ]
        );

        if ($currentProgress->status === 'not_started') {
            $currentProgress->update([
                'status' => 'in_progress',
                'progress' => max((int) $currentProgress->progress, 25),
                'started_at' => $currentProgress->started_at ?? now(),
            ]);
        }

        // Map sequential unlock state for each module strictly:
        // Module N cannot be accessed if Module N-1 status != 'completed'
        $moduleNav = $allModules->map(function ($mod, $index) use ($allModules, $completedModuleIds, $progressList, $module) {
            $isCurrent = $mod->id === $module->id;
            $prog = $progressList->get($mod->id);
            $isCompleted = in_array($mod->id, $completedModuleIds);
            $isInProgress = $prog && $prog->status === 'in_progress';
            $progressPercentage = $prog ? (int) $prog->progress : ($isCompleted ? 100 : 0);

            // Strict Sequencing Rule:
            // Module 0 (or sequence <= 1) is unlocked.
            // For index > 0, preceding module (N-1) must have status == 'completed'
            $isUnlocked = false;
            if ($index === 0 || $mod->sequence <= 1) {
                $isUnlocked = true;
            } else {
                $precedingModule = $allModules->get($index - 1);
                $isUnlocked = $precedingModule && in_array($precedingModule->id, $completedModuleIds);
            }

            return (object) [
                'id' => $mod->id,
                'title' => $mod->title,
                'sequence' => $mod->sequence,
                'duration' => $mod->estimated_duration ?? 30,
                'is_current' => $isCurrent,
                'is_unlocked' => $isUnlocked,
                'is_completed' => $isCompleted,
                'is_in_progress' => $isInProgress,
                'progress_percentage' => $progressPercentage,
            ];
        });

        // Determine Next and Previous modules in workshop sequence
        $currentIndex = $allModules->search(fn ($m) => $m->id === $module->id);
        $previousModule = ($currentIndex !== false && $currentIndex > 0) ? $allModules->get($currentIndex - 1) : null;
        $nextModule = ($currentIndex !== false && $currentIndex < $allModules->count() - 1) ? $allModules->get($currentIndex + 1) : null;

        // Check if all modules are completed
        $totalRequired = $allModules->where('is_required', true)->count();
        $totalCompleted = count(array_intersect($allModules->where('is_required', true)->pluck('id')->toArray(), $completedModuleIds));
        $allCompleted = $totalRequired > 0 && $totalCompleted >= $totalRequired;
        $questions = collect();
        $latestQuizAttempt = null;
        $quizAttemptsLeft = 0;
        $canAttemptQuiz = false;

        if ($assessment) {
            $latestQuizAttempt = AssessmentAttempt::where('assessment_id', $assessment->id)
                ->where('workshop_id', $workshop->id)
                ->where(function ($query) use ($teacher) {
                    $query->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
                })
                ->latest('attempt_number')
                ->first();

            $attemptCount = AssessmentAttempt::where('assessment_id', $assessment->id)
                ->where('workshop_id', $workshop->id)
                ->where(function ($query) use ($teacher) {
                    $query->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
                })
                ->count();

            $quizAttemptsLeft = max(0, $assessment->max_attempts - $attemptCount);
            $canAttemptQuiz = $assessment->is_published
                && $quizAttemptsLeft > 0
                && $latestQuizAttempt?->result !== 'passed'
                && Gate::allows('attempt', [$assessment, $workshop]);

            if ($canAttemptQuiz) {
                $questions = $assessment->questions()
                    ->with(['choices' => fn ($query) => $query->inRandomOrder()])
                    ->orderBy('sequence')
                    ->get();
            }
        }

        $quizResult = $request->session()->get('module_quiz_result');
        $hasCertification = Certification::where('workshop_id', $workshop->id)
            ->where(function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
            })
            ->where('status', 'approved')
            ->exists();

        return view('teacher.modules.show', compact(
            'workshop',
            'module',
            'moduleNav',
            'currentProgress',
            'previousModule',
            'nextModule',
            'allCompleted',
            'assessment',
            'questions',
            'latestQuizAttempt',
            'quizAttemptsLeft',
            'canAttemptQuiz',
            'quizResult',
            'hasCertification'
        ));
    }

    /**
     * Mark a module as 100% completed and advance to the next module.
     */
    public function complete(Request $request, Workshop $workshop, Module $module): RedirectResponse
    {
        $teacher = $request->user();
        Gate::authorize('complete', [$module, $workshop]);

        return redirect()
            ->route('teacher.learning.module', [$workshop, $module])
            ->with('warning', 'Pass the required module quiz to complete this module and unlock the next one.');
    }

    /**
     * Track interaction with a material (PDF, presentation, video).
     * Rule: When a teacher interacts with materials, update module_progress.progress to 100% and status to completed.
     */
    public function interactMaterial(Request $request, Workshop $workshop, Module $module, Material $material)
    {
        $teacher = $request->user();
        Gate::authorize('view', [$module, $workshop]);

        if ($material->module_id !== $module->id) {
            abort(404, 'Material not found in this module.');
        }

        $this->recordMaterialProgress($workshop, $module, $teacher->id);
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Material '{$material->title}' completed! Pass the module quiz to unlock the next module.",
                'progress' => 75,
                'status' => 'in_progress',
            ]);
        }

        return back()->with('success', "Material '{$material->title}' completed. Pass the module quiz to unlock the next module.");
    }

    /**
     * Preview or download a training material file securely.
     */
    public function downloadMaterial(Request $request, Workshop $workshop, Module $module, Material $material)
    {
        $teacher = $request->user();
        Gate::authorize('view', [$module, $workshop]);

        if ($material->module_id !== $module->id) {
            abort(404, 'Material not found in this module.');
        }

        $this->recordMaterialProgress($workshop, $module, $teacher->id);

        if ($material->external_url) {
            return redirect()->away($material->external_url);
        }

        if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
            return Storage::disk('public')->download($material->file_path, $material->title);
        }

        return back()->with(
            'success',
            "Training resource '{$material->title}' accessed. Pass the module quiz to unlock the next module."
        );
    }

    private function recordMaterialProgress(Workshop $workshop, Module $module, int $teacherId): void
    {
        $progress = ModuleProgress::firstOrNew([
            'workshop_id' => $workshop->id,
            'module_id' => $module->id,
            'teacher_id' => $teacherId,
        ]);

        $progress->user_id = $teacherId;
        $progress->progress = max(75, (int) $progress->progress);
        $progress->status = 'in_progress';
        $progress->completed_at = null;
        $progress->save();
    }
}
