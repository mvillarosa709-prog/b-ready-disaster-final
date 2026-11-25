<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Certification;
use App\Models\Choice;
use App\Models\Course;
use App\Models\Module;
use App\Models\ModuleProgress;
use App\Models\Question;
use App\Models\Workshop;
use App\Models\WorkshopTeacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class WorkshopController extends Controller
{
    /**
     * Display a listing of available published workshops and published course modules for discovery.
     */
    public function index(Request $request): View
    {
        $teacher = $request->user();
        $search = trim((string) $request->input('search', ''));

        $query = Workshop::whereIn('status', ['published', 'ongoing'])
            ->whereHas('course', fn ($query) => $query->published())
            ->with(['trainer', 'course.modules', 'assessment']);

        // Search across workshop title/description, course title, and module titles/descriptions
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('course', function ($cq) use ($search) {
                        $cq->where('title', 'like', "%{$search}%");
                    })
                    ->orWhereHas('course.modules', function ($mq) use ($search) {
                        $mq->where('title', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%");
                    });
            });
        }

        $workshops = $query->latest('start_date')->paginate(9)->withQueryString();

        // Fetch enrolled workshop IDs for current teacher (check workshop_teachers)
        $enrolledWorkshopIds = WorkshopTeacher::where(function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
        })
            ->whereIn('status', ['registered', 'completed'])
            ->pluck('workshop_id')
            ->toArray();

        return view('teacher.workshops.index', compact('workshops', 'enrolledWorkshopIds'));
    }

    /**
     * Display the workshops the authenticated teacher has enrolled in.
     */
    public function myWorkshops(Request $request): View
    {
        $teacher = $request->user();

        $enrollments = WorkshopTeacher::where(function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
        })
            ->whereHas('workshop.course', fn ($query) => $query->published())
            ->with([
                'workshop.trainer',
                'workshop.course.modules',
                'workshop.assessment',
                'workshop.classroomPackage',
            ])
            ->latest('joined_at')
            ->get();

        $activeWorkshops = [];
        $completedWorkshops = [];

        foreach ($enrollments as $enrollment) {
            $workshop = $enrollment->workshop;
            if (! $workshop) {
                continue;
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

            $isCompleted = $enrollment->status === 'completed' || $certification !== null;

            $item = (object) [
                'workshop' => $workshop,
                'enrollment' => $enrollment,
                'total_modules' => $totalModules,
                'completed_modules' => $completedCount,
                'progress_percentage' => $percentage,
                'next_module' => $nextModule,
                'certification' => $certification,
                'is_completed' => $isCompleted,
            ];

            if ($isCompleted) {
                $completedWorkshops[] = $item;
            } else {
                $activeWorkshops[] = $item;
            }
        }

        return view('teacher.workshops.my-workshops', compact('activeWorkshops', 'completedWorkshops'));
    }

    /**
     * Display detailed syllabus, sequential module status, and gating for a workshop.
     * Enforces Strict Sequencing Rule: If Module N-1 status != 'completed', mark Module N as Locked.
     */
    public function show(Request $request, Workshop $workshop): View
    {
        $teacher = $request->user();
        Gate::authorize('view', $workshop);

        $workshop->load(['trainer', 'course.modules.materials', 'assessment', 'classroomPackage']);

        // Check enrollment
        $enrollment = WorkshopTeacher::where('workshop_id', $workshop->id)
            ->where(function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
            })
            ->first();

        $isEnrolled = $enrollment && in_array($enrollment->status, ['registered', 'completed']);

        // Progress breakdown
        $modules = $workshop->ordered_modules->sortBy('sequence')->values();
        $completedModuleIds = [];
        $inProgressModuleIds = [];
        $progressMap = collect();

        if ($isEnrolled) {
            $progressRecords = ModuleProgress::where('workshop_id', $workshop->id)
                ->where(function ($q) use ($teacher) {
                    $q->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
                })
                ->get();

            $completedModuleIds = $progressRecords->where('status', 'completed')->pluck('module_id')->toArray();
            $inProgressModuleIds = $progressRecords->where('status', 'in_progress')->pluck('module_id')->toArray();
            $progressMap = $progressRecords->keyBy('module_id');
        }

        // Map sequential unlock state for each module strictly:
        // Module N cannot be accessed if Module N-1 status != 'completed'
        $modulesWithAccess = $modules->map(function ($mod, $index) use ($modules, $completedModuleIds, $inProgressModuleIds, $progressMap, $isEnrolled) {
            $isCompleted = in_array($mod->id, $completedModuleIds);
            $isInProgress = in_array($mod->id, $inProgressModuleIds);
            $modProgress = $progressMap->get($mod->id);
            $progressPercentage = $modProgress ? (int) $modProgress->progress : ($isCompleted ? 100 : 0);

            // Strict Sequencing Rule:
            // Module 0 (or sequence <= 1) is unlocked by default if enrolled.
            // For N > 0, check if Module N-1 status == 'completed'
            $isUnlocked = false;
            if ($isEnrolled) {
                if ($index === 0 || $mod->sequence <= 1) {
                    $isUnlocked = true;
                } else {
                    $precedingModule = $modules->get($index - 1);
                    $isUnlocked = $precedingModule && in_array($precedingModule->id, $completedModuleIds);
                }
            }

            return (object) [
                'module' => $mod,
                'is_unlocked' => $isUnlocked,
                'is_completed' => $isCompleted,
                'is_in_progress' => $isInProgress,
                'progress_percentage' => $progressPercentage,
            ];
        });

        // Assessment status: evaluate if all required modules have module_progress.status == 'completed' (100% completion)
        $allRequiredCompleted = false;
        if ($isEnrolled && $modules->isNotEmpty()) {
            $requiredModules = $modules->where('is_required', true);
            $requiredModuleIds = $requiredModules->pluck('id')->toArray();
            $diff = array_diff($requiredModuleIds, $completedModuleIds);
            $allRequiredCompleted = empty($diff);
        }

        $assessment = $workshop->assessment;
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

        $attemptCount = 0;
        $latestAttempt = null;
        $certification = null;

        if ($isEnrolled) {
            if ($assessment) {
                $attemptCount = AssessmentAttempt::where('assessment_id', $assessment->id)
                    ->where('workshop_id', $workshop->id)
                    ->where(function ($q) use ($teacher) {
                        $q->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
                    })
                    ->count();

                $latestAttempt = AssessmentAttempt::where('assessment_id', $assessment->id)
                    ->where('workshop_id', $workshop->id)
                    ->where(function ($q) use ($teacher) {
                        $q->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
                    })
                    ->latest('id')
                    ->first();
            }

            $certification = Certification::where(function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
            })
                ->where('workshop_id', $workshop->id)
                ->first();
        }

        $package = $workshop->classroomPackage;
        $isPackageUnlocked = $certification !== null;

        $firstModule = $modules->sortBy('sequence')->first();
        $firstUnlockedItem = $modulesWithAccess->first(fn ($item) => $item->is_unlocked && ! $item->is_completed)
            ?? $modulesWithAccess->firstWhere('is_unlocked', true);
        $firstUnlockedModule = $firstUnlockedItem ? $firstUnlockedItem->module : null;

        return view('teacher.workshops.show', compact(
            'workshop',
            'isEnrolled',
            'enrollment',
            'modulesWithAccess',
            'allRequiredCompleted',
            'assessment',
            'attemptCount',
            'latestAttempt',
            'certification',
            'package',
            'isPackageUnlocked',
            'firstModule',
            'firstUnlockedModule'
        ));
    }

    /**
     * Enroll the authenticated teacher in a workshop.
     * Inserts a record into workshop_teachers (user_id, workshop_id). Prevents duplicate entries.
     * Automatically redirects the teacher to the first module in sequence.
     */
    public function join(Request $request, Workshop $workshop): RedirectResponse
    {
        $teacher = $request->user();
        Gate::authorize('join', $workshop);

        // Prevent duplicate entries
        $existing = WorkshopTeacher::where('workshop_id', $workshop->id)
            ->where(function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
            })
            ->first();

        if ($existing && in_array($existing->status, ['registered', 'completed'])) {
            return redirect()->route('teacher.workshops.show', $workshop)
                ->with('info', "You are already registered for {$workshop->title}.");
        }

        // Insert record into workshop_teachers (user_id, workshop_id)
        WorkshopTeacher::updateOrCreate(
            ['workshop_id' => $workshop->id, 'teacher_id' => $teacher->id],
            [
                'user_id' => $teacher->id,
                'status' => 'registered',
                'joined_at' => now(),
            ]
        );

        // Pre-initialize ModuleProgress for first module
        $modules = $workshop->ordered_modules;
        $firstModule = null;
        if ($modules->isNotEmpty()) {
            $firstModule = $modules->sortBy('sequence')->first();
            if ($firstModule) {
                ModuleProgress::firstOrCreate(
                    [
                        'workshop_id' => $workshop->id,
                        'module_id' => $firstModule->id,
                        'teacher_id' => $teacher->id,
                    ],
                    [
                        'user_id' => $teacher->id,
                        'progress' => 0,
                        'status' => 'not_started',
                    ]
                );
            }
        }

        // Directly redirect to Module 1 if modules exist, fulfilling "enroll first then proceed the modules starting at first"
        if ($firstModule && ! $request->boolean('stay_on_overview')) {
            return redirect()->route('teacher.learning.module', [$workshop, $firstModule])
                ->with('success', "You have successfully registered for {$workshop->title}! Your training course begins with Module {$firstModule->sequence}: {$firstModule->title}.");
        }

        return redirect()->route('teacher.workshops.show', $workshop)
            ->with('success', "You have successfully registered for {$workshop->title}! Your training modules are ready.");
    }
}
