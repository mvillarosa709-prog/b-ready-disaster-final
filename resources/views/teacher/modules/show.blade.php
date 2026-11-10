<x-dynamic-component component="layouts.teacher">
    <x-slot name="title">Module {{ $module->sequence }}: {{ $module->title }}</x-slot>

    <!-- Header Banner (Matches Trainer Side) -->
    <div class="bg-white border-b border-slate-200/80 px-4 py-6 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2">
                    <a href="{{ route('teacher.workshops.show', $workshop) }}" class="hover:text-emerald-600 transition font-medium">
                        {{ $workshop->title }}
                    </a>
                    <span>/</span>
                    <span class="text-slate-800 font-semibold">Module {{ $module->sequence }}</span>
                </nav>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Module {{ $module->sequence }}: {{ $module->title }}</h1>
            </div>
            <div>
                <a href="{{ route('teacher.workshops.show', $workshop) }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition shadow-xs">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Workshop
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 space-y-6">
        @if ($quizResult && (int) ($quizResult['module_id'] ?? 0) !== (int) $module->id)
            <div class="rounded-2xl border p-5 {{ $quizResult['passed'] ? 'border-emerald-200 bg-emerald-50 text-emerald-900' : 'border-rose-200 bg-rose-50 text-rose-900' }}">
                <h2 class="font-bold">
                    {{ $quizResult['passed'] ? 'Quiz passed' : 'Quiz not passed yet' }}
                    &mdash; {{ number_format($quizResult['percentage'], 2) }}%
                </h2>
                <p class="mt-1 text-sm">
                    Score {{ $quizResult['score'] }} / {{ $quizResult['total_points'] }} points.
                    Passing score: {{ $quizResult['passing_score'] }}%.
                    @if ($quizResult['passed'] && ! empty($quizResult['next_module_title']))
                        {{ $quizResult['next_module_title'] }} is now unlocked.
                    @elseif (! $quizResult['passed'] && $quizResult['attempts_left'] > 0)
                        You have {{ $quizResult['attempts_left'] }} retake(s) remaining.
                    @elseif (! $quizResult['passed'])
                        No attempts remain. Contact your trainer for support.
                    @endif
                </p>
            </div>
        @endif

        <!-- Module Progress Count -->
        <div class="flex items-center justify-end">
            <div class="text-xs font-semibold text-slate-500">
                Module <span class="text-slate-800">{{ $module->sequence }}</span> of <span class="text-slate-800">{{ $moduleNav->count() }}</span>
            </div>
        </div>

        <!-- Split Screen Layout: Left Module Sidebar / Right Active Module Content -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Left 4 Cols: Sequential Course Navigator -->
            <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <div class="mb-4 pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900">Module Sequence</h3>
                    <p class="text-xs text-slate-500">Modules unlock strictly in sequential order.</p>
                </div>

                <div class="space-y-2">
                    @foreach ($moduleNav as $item)
                        @if ($item->is_unlocked)
                            <a href="{{ route('teacher.learning.module', [$workshop, $item->id]) }}"
                               class="flex items-start gap-3 p-3 rounded-xl transition-all {{ $item->is_current ? 'bg-emerald-50/80 border border-emerald-200 text-emerald-900' : 'hover:bg-slate-50 text-slate-700' }}">
                                <div class="h-7 w-7 rounded-lg shrink-0 flex items-center justify-center text-xs font-bold {{ $item->is_completed ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : ($item->is_current ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-slate-100 text-slate-600') }}">
                                    @if ($item->is_completed)
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @else
                                        {{ $item->sequence }}
                                    @endif
                                </div>
                                <div class="overflow-hidden">
                                    <div class="text-xs font-bold truncate {{ $item->is_current ? 'text-emerald-950 font-extrabold' : '' }}">
                                        {{ $item->title }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        {{ $item->duration }} mins &bull; 
                                        @if ($item->is_completed)
                                            <span class="text-emerald-700 font-semibold">Completed</span>
                                        @elseif ($item->is_current)
                                            <span class="text-emerald-700 font-semibold">Currently Viewing</span>
                                        @else
                                            <span>Unlocked</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @else
                            <!-- Locked Module Item -->
                            <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100 text-slate-400 opacity-60 cursor-not-allowed">
                                <div class="h-7 w-7 rounded-lg shrink-0 bg-slate-200 text-slate-500 flex items-center justify-center text-xs font-bold">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-xs font-semibold text-slate-500 truncate">{{ $item->title }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">🔒 Complete Module {{ $item->sequence - 1 }} first</div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Right 8 Cols: Active Module Content & Training Materials -->
            <div class="lg:col-span-8 space-y-6">
                <!-- Module Header & Description -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-5">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-4">
                        <div class="flex items-center gap-2">
                            <span class="inline-block text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-md px-2.5 py-1">
                                Sequence {{ $module->sequence }}
                            </span>
                            <span class="text-xs text-slate-500 font-medium">Estimated: {{ $module->estimated_duration ?? '30' }} Minutes</span>
                        </div>

                        @if ($currentProgress && $currentProgress->status === 'completed')
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 border border-emerald-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                Module Completed
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700 border border-amber-200">
                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-ping"></span>
                                In Progress
                            </span>
                        @endif
                    </div>

                    <div>
                        <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                            {{ $module->title }}
                        </h2>
                        <div class="mt-4 text-sm text-slate-700 leading-relaxed space-y-3">
                            <p>{{ $module->description }}</p>
                        </div>
                    </div>

                    <!-- Visual Progress Bar for Active Module -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                        <div class="flex items-center justify-between text-xs font-semibold mb-1.5">
                            <span class="text-slate-700">Module Completion Progress:</span>
                            <span class="font-bold text-emerald-700 font-mono">{{ $currentProgress ? $currentProgress->progress : 0 }}%</span>
                        </div>
                        <div class="w-full h-2.5 bg-slate-200 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full transition-all duration-500" style="width: {{ $currentProgress ? $currentProgress->progress : 0 }}%"></div>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-2">
                            <span class="font-semibold text-slate-700">Sequential Tracking:</span>                             Materials update your progress. If this module has a quiz, you must pass it to complete the module and unlock the next one.
                        </p>
                    </div>

                    <!-- Learning Objectives -->
                    @if ($module->learning_objectives)
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Key Learning Objectives
                            </h4>
                            <div class="mt-2 text-xs text-slate-600 leading-relaxed whitespace-pre-line">
                                {{ $module->learning_objectives }}
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Training Materials Section -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Training Materials & Resources</h3>
                            <p class="text-xs text-slate-500">Download or preview learning assets curated for this module.</p>
                        </div>
                        <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2 py-1 rounded-md">
                            {{ $module->materials->count() }} Files
                        </span>
                    </div>

                    @if ($module->materials->isEmpty())
                        <div class="p-6 text-center text-xs text-slate-400 border border-dashed border-slate-200 rounded-xl">
                            All necessary content for this module is provided in the lecture overview above.
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach ($module->materials as $mat)
                                @php
                                    $type = strtolower($mat->type ?? 'pdf');
                                @endphp

                                <div class="p-4 rounded-xl border border-slate-200/80 bg-slate-50/50 hover:bg-slate-50 hover:border-emerald-200 transition-all flex flex-col justify-between">
                                    <div class="flex items-start gap-3">
                                        <!-- File format icon -->
                                        <div class="h-10 w-10 rounded-xl flex items-center justify-center shrink-0 
                                            @if ($type === 'pdf') bg-rose-50 text-rose-600 border border-rose-200
                                            @elseif ($type === 'presentation') bg-amber-50 text-amber-600 border border-amber-200
                                            @elseif ($type === 'video') bg-sky-50 text-sky-600 border border-sky-200
                                            @else bg-purple-50 text-purple-600 border border-purple-200 @endif">
                                            @if ($type === 'pdf')
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                </svg>
                                            @elseif ($type === 'presentation')
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                                                </svg>
                                            @elseif ($type === 'video')
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                </svg>
                                            @endif
                                        </div>

                                        <div class="overflow-hidden">
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">
                                                {{ ucfirst($type) }}
                                            </span>
                                            <h5 class="text-xs font-bold text-slate-800 truncate mt-0.5">{{ $mat->title }}</h5>
                                            @if ($mat->description)
                                                <p class="text-[11px] text-slate-500 mt-1 line-clamp-2">{{ $mat->description }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mt-4 pt-3 border-t border-slate-200/60 flex items-center justify-between gap-2">
                                        <form method="POST" action="{{ route('teacher.learning.material.interact', [$workshop, $module, $mat]) }}">
                                            @csrf
                                            <button type="submit" 
                                                    class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-600 hover:text-emerald-700 bg-white hover:bg-emerald-50 px-2.5 py-1.5 rounded-lg border border-slate-200 hover:border-emerald-200 transition-colors shadow-2xs">
                                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Mark Done
                                            </button>
                                        </form>

                                        <a href="{{ route('teacher.learning.material.download', [$workshop, $module, $mat]) }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600 hover:text-emerald-800 transition-colors bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-lg border border-emerald-200">
                                            <span>Access Resource</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                @if ($assessment)
                    <section id="module-quiz" class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-5">
                        <div class="flex flex-wrap items-start justify-between gap-3 border-b border-slate-100 pb-4">
                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-100">
                                    Module Quiz
                                </span>
                                <h3 class="mt-2 text-lg font-bold text-slate-900">{{ $assessment->title }}</h3>
                                <p class="mt-1 text-xs text-slate-600">
                                    {{ $assessment->description ?? 'Answer the questions below. A passing score completes this module and unlocks the next one.' }}
                                </p>
                            </div>
                            <div class="text-right text-xs text-slate-600">
                                <div>Passing: <strong>{{ $assessment->passing_score }}%</strong></div>
                                <div>Attempts left: <strong>{{ $quizAttemptsLeft }}</strong></div>
                            </div>
                        </div>

                        @if ($latestQuizAttempt)
                            <div class="rounded-xl border p-4 {{ $latestQuizAttempt->result === 'passed' ? 'border-emerald-200 bg-emerald-50 text-emerald-900' : 'border-rose-200 bg-rose-50 text-rose-900' }}">
                                <div class="flex flex-wrap items-start justify-between gap-2">
                                    <div>
                                        <h4 class="font-bold">Your latest quiz result</h4>
                                        <p class="mt-1 text-sm">
                                            Score {{ number_format((float) $latestQuizAttempt->score, 2) }} / {{ number_format($latestQuizAttempt->total_points, 2) }} points
                                            ({{ number_format($latestQuizAttempt->percentage, 2) }}%).
                                            Passing score: {{ $assessment->passing_score }}%.
                                        </p>
                                    </div>
                                    <span class="text-xs font-bold uppercase">
                                        {{ ucfirst($latestQuizAttempt->result) }}
                                    </span>
                                </div>
                                <p class="mt-1 text-xs opacity-80">
                                    Attempt #{{ $latestQuizAttempt->attempt_number }}
                                    @if ($latestQuizAttempt->submitted_at)
                                        · Submitted {{ $latestQuizAttempt->submitted_at->format('M d, Y h:i A') }}
                                    @endif
                                </p>
                            </div>
                        @endif

                        @if ($latestQuizAttempt?->result === 'passed')
                            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                                This module is complete. Your score is shown above.
                                @if ($hasCertification)
                                    <a class="ml-1 font-bold underline" href="{{ route('teacher.assessments.certificate', $workshop) }}" target="_blank">View certificate</a>
                                @endif
                            </div>
                        @elseif (! $assessment->is_published)
                            <p class="text-sm text-slate-500">This quiz is not available yet.</p>
                        @elseif (! $canAttemptQuiz)
                            <p class="text-sm text-rose-700">
                                {{ $quizAttemptsLeft > 0 ? 'Complete the previous required modules before taking this quiz.' : 'No attempts remain. Contact your trainer for support.' }}
                            </p>
                        @else
                            <form method="POST" action="{{ route('teacher.learning.module.assessment.submit', [$workshop, $module]) }}" class="space-y-5">
                                @csrf
                                @foreach ($questions as $index => $question)
                                    <fieldset class="rounded-xl border border-slate-200 p-4">
                                        <legend class="px-1 text-sm font-semibold text-slate-900">
                                            {{ $index + 1 }}. {{ $question->question_text }}
                                            <span class="text-xs font-normal text-slate-500">({{ $question->points }} pts)</span>
                                        </legend>
                                        @if ($question->question_type === 'fill_blank')
                                            <input type="text" name="answers[{{ $question->id }}]" maxlength="1000" required
                                                value="{{ old('answers.' . $question->id) }}"
                                                placeholder="Type your answer"
                                                class="mt-3 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                        @else
                                            <div class="mt-3 space-y-2">
                                                @foreach ($question->choices as $choice)
                                                    <label class="flex items-start gap-2 rounded-lg border border-slate-200 p-3 text-sm hover:bg-slate-50">
                                                        <input type="radio" name="answers[{{ $question->id }}]" value="{{ $choice->id }}"
                                                            @checked((string) old('answers.' . $question->id) === (string) $choice->id)
                                                            required class="mt-1 text-emerald-600 focus:ring-emerald-500">
                                                        <span>{{ $choice->choice_text }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @endif
                                    </fieldset>
                                @endforeach

                                @error('answers')
                                    <p class="text-sm text-rose-600">{{ $message }}</p>
                                @enderror
                                <button type="submit" class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-emerald-700">
                                    {{ $latestQuizAttempt ? 'Retake Quiz' : 'Submit Quiz' }}
                                </button>
                            </form>
                        @endif
                    </section>
                @else
                    <section id="module-quiz" class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-900">
                        The required quiz for this module is being prepared. Contact your trainer before continuing.
                    </section>
                @endif

                <!-- Bottom Navigation / Complete Module Action Bar -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div>
                        @if ($previousModule)
                            <a href="{{ route('teacher.learning.module', [$workshop, $previousModule]) }}"
                               class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 px-3 py-2 rounded-xl hover:bg-slate-100 transition-colors">
                                &larr; Previous: Module {{ $previousModule->sequence }}
                            </a>
                        @else
                            <span class="text-xs text-slate-400">First Module</span>
                        @endif
                    </div>

                    <div class="flex items-center gap-3">
                        @if ($currentProgress && $currentProgress->status === 'completed')
                            @if ($nextModule)
                                <a href="{{ route('teacher.learning.module', [$workshop, $nextModule]) }}"
                                   class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-xs font-bold text-white px-5 py-2.5 transition-colors shadow-xs">
                                    Next: Module {{ $nextModule->sequence }} &rarr;
                                </a>
                            @else
                                <a href="{{ route('teacher.workshops.show', $workshop) }}"
                                   class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-xs font-bold text-white px-5 py-2.5 transition-colors shadow-xs">
                                    Workshop Overview
                                </a>
                            @endif
                        @elseif ($assessment)
                            <a href="#module-quiz"
                               class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-xs font-bold text-white px-5 py-2.5 transition-colors shadow-xs">
                                Pass Module Quiz to Continue &darr;
                            </a>
                        @else
                            <span class="text-xs font-semibold text-amber-700">Required quiz is not available yet</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
