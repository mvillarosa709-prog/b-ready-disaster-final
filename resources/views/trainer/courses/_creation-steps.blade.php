@php
    $step = $activeStep ?? 1;
    $courseModel = $course ?? null;

    if ($courseModel) {
        $courseModel->loadMissing(['modules.materials', 'modules.quiz.questions.choices']);
    }

    $hasCourse = (bool) $courseModel;
    $modulesCount = $courseModel ? $courseModel->modules->count() : 0;
    $hasModules = $modulesCount > 0;
    $hasMaterials = $courseModel ? $courseModel->modules->contains(fn ($m) => $m->materials->isNotEmpty()) : false;
    $quizzesReady = $courseModel ? $courseModel->hasReadyModuleQuizzes() : false;
    $isPublished = $courseModel && $courseModel->status === 'published';

    $steps = [
        [
            'num' => 1,
            'title' => 'Course Information',
            'done' => $hasCourse,
            'active' => $step === 1,
            'subtitle' => $step === 1 ? 'Currently completing' : ($hasCourse ? 'Saved as draft' : 'Title, objectives & audience'),
        ],
        [
            'num' => 2,
            'title' => 'Add Learning Modules',
            'done' => $hasModules && $step !== 2,
            'active' => $step === 2,
            'subtitle' => $step === 2
                ? 'Currently completing'
                : ($hasModules ? $modulesCount . ' ' . \Illuminate\Support\Str::plural('module', $modulesCount) . ' added' : 'Sequential topics & lessons'),
        ],
        [
            'num' => 3,
            'title' => 'Attach Materials',
            'done' => $hasMaterials && $step !== 3,
            'active' => $step === 3,
            'subtitle' => $step === 3
                ? 'Currently completing'
                : ($hasMaterials ? 'Materials attached' : 'PDF guides, slides, hazard maps'),
        ],
        [
            'num' => 4,
            'title' => 'Set Module Quizzes',
            'done' => $quizzesReady && $step !== 4,
            'active' => $step === 4,
            'subtitle' => $step === 4
                ? 'Currently completing'
                : ($quizzesReady ? 'All module quizzes published' : 'Evaluation questions & 75% passing'),
        ],
        [
            'num' => 5,
            'title' => $isPublished ? 'Course Published' : 'Final Publication',
            'done' => $isPublished,
            'active' => $step === 5 && ! $isPublished,
            'subtitle' => $isPublished
                ? 'Published & live for workshops'
                : ($quizzesReady ? 'Ready — click the publish button above' : 'Make available for teacher workshops'),
        ],
    ];
@endphp

<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
    <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-700 mb-4 flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
        Course Creation Steps
    </h4>

    <ol class="relative border-l border-emerald-200 ml-3 space-y-6 text-sm">
        @foreach ($steps as $item)
            <li class="ml-6">
                @if ($item['done'])
                    <span class="absolute -left-3 flex items-center justify-center w-6 h-6 rounded-full bg-emerald-600 text-white text-xs font-bold ring-4 ring-white">
                        ✓
                    </span>
                    <h5 class="font-bold text-slate-900">{{ $item['title'] }}</h5>
                    <p class="text-xs text-emerald-700 mt-0.5 font-medium">{{ $item['subtitle'] }}</p>
                @elseif ($item['active'])
                    <span class="absolute -left-3 flex items-center justify-center w-6 h-6 rounded-full bg-emerald-600 text-white text-xs font-bold ring-4 ring-white">
                        {{ $item['num'] }}
                    </span>
                    <h5 class="font-bold text-slate-900">{{ $item['title'] }}</h5>
                    <p class="text-xs text-emerald-700 mt-0.5 font-medium">{{ $item['subtitle'] }}</p>
                @else
                    <span class="absolute -left-3 flex items-center justify-center w-6 h-6 rounded-full bg-slate-100 text-slate-500 text-xs font-bold ring-4 ring-white">
                        {{ $item['num'] }}
                    </span>
                    <h5 class="font-semibold text-slate-700">{{ $item['title'] }}</h5>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $item['subtitle'] }}</p>
                @endif
            </li>
        @endforeach
    </ol>
</div>
