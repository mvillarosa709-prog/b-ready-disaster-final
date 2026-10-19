<x-dynamic-component component="layouts.teacher">
    <x-slot name="title">{{ $workshop->title }}</x-slot>
    <x-slot name="header">{{ $workshop->title }}</x-slot>

    <div class="space-y-8">
        <!-- Back Button -->
        <div>
            <a href="{{ url()->previous() }}"
               class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-indigo-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </a>
        </div>

        <!-- Top Workshop Header Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="max-w-3xl">
                    <div class="flex items-center gap-2">
                        <span class="inline-block text-xs font-bold text-indigo-700 bg-indigo-50 border border-indigo-100 rounded-md px-2.5 py-1">
                            {{ $workshop->course->title ?? 'DRR Training Course' }}
                        </span>
                        <span class="inline-block text-xs font-semibold text-slate-500">
                            {{ $workshop->course->estimated_duration ?? '4' }} Hours Total
                        </span>
                    </div>

                    <h2 class="mt-3 text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        {{ $workshop->title }}
                    </h2>

                    <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                        {{ $workshop->description ?? ($workshop->course->description ?? 'Disaster Preparedness and Risk Reduction Training.') }}
                    </p>

                    <!-- Trainer details -->
                    <div class="mt-5 flex items-center gap-3">
                        <div class="h-9 w-9 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-sm border border-indigo-200">
                            {{ substr($workshop->trainer->name ?? 'T', 0, 1) }}
                        </div>
                        <div class="text-xs">
                            <span class="text-slate-400">Lead Trainer:</span>
                            <span class="font-bold text-slate-800 ml-1">{{ $workshop->trainer->name ?? 'DRR Expert' }}</span>
                            <span class="text-slate-400 ml-2">({{ $workshop->trainer->email ?? '' }})</span>
                        </div>
                    </div>
                </div>

                <!-- Right Action Block -->
                <div class="lg:w-72 shrink-0 p-5 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Your Registration</span>
                        @if ($isEnrolled)
                            <div class="mt-2 flex items-center gap-2 text-emerald-700 font-bold text-sm">
                                <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                                Active Learner
                            </div>
                            <div class="text-[11px] text-slate-500 mt-1">
                                Registered on {{ $enrollment->joined_at ? $enrollment->joined_at->format('M d, Y') : now()->format('M d, Y') }}
                            </div>
                        @else
                            <div class="mt-2 text-sm font-semibold text-slate-700">
                                Not registered yet
                            </div>
                            <div class="text-[11px] text-slate-500 mt-1">
                                Registration Deadline: {{ $workshop->registration_deadline ? $workshop->registration_deadline->format('M d, Y') : 'Open' }}
                            </div>
                        @endif
                    </div>

                    <div class="mt-5">
                        @if ($isEnrolled)
                            @if ($firstUnlockedModule)
                                <a href="{{ route('teacher.learning.module', [$workshop, $firstUnlockedModule]) }}"
                                   class="w-full py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-xs font-bold text-white shadow-xs transition-colors flex items-center justify-center gap-1.5">
                                    <span>{{ $firstUnlockedModule->sequence === 1 ? 'Start Course (Module 1)' : 'Continue to Module ' . $firstUnlockedModule->sequence }}</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                    </svg>
                                </a>
                            @else
                                <span class="block text-center text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 py-2 rounded-xl">
                                    Registered &bull; Access Active
                                </span>
                            @endif
                        @else
                            <form method="POST" action="{{ route('teacher.workshops.register', $workshop) }}">
                                @csrf
                                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-xs font-bold text-white shadow-xs transition-colors flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Register for Workshop Now
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- 2 Column Layout: Modules List (Left) vs Assessment & Classroom Package Gating (Right) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left 2 Cols: Sequential Learning Modules -->
            <div class="lg:col-span-2 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Course Syllabus & Sequential Modules</h3>
                        <p class="text-xs text-slate-500">Modules must be completed sequentially to unlock the final assessment.</p>
                    </div>
                </div>

                @if (! $isEnrolled)
                    <div class="p-4 rounded-2xl bg-indigo-50/80 border border-indigo-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <span class="p-2 rounded-xl bg-indigo-600 text-white shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </span>
                            <div>
                                <div class="text-xs font-bold text-indigo-950">Registration Required to Begin</div>
                                <div class="text-[11px] text-indigo-800">Register first to take this training course. Once registered, you will start immediately with Module 1.</div>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('teacher.workshops.register', $workshop) }}" class="shrink-0">
                            @csrf
                            <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs transition-colors flex items-center justify-center gap-1.5">
                                <span>Register Now &rarr;</span>
                            </button>
                        </form>
                    </div>
                @endif

                @if ($modulesWithAccess->isEmpty())
                    <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center text-slate-500 text-xs">
                        No modules currently published for this workshop.
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($modulesWithAccess as $item)
                            @php
                                $mod = $item->module;
                            @endphp

                            <div class="rounded-2xl border p-5 transition-all {{ $item->is_unlocked ? 'bg-white border-slate-200/90 shadow-2xs hover:border-indigo-300' : 'bg-slate-50 border-slate-200 opacity-60 cursor-not-allowed' }}">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="flex items-start gap-4">
                                        <!-- Sequence Icon Badge -->
                                        <div class="h-10 w-10 shrink-0 rounded-xl flex items-center justify-center font-bold text-sm {{ $item->is_completed ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : ($item->is_unlocked ? 'bg-indigo-100 text-indigo-700 border border-indigo-200' : 'bg-slate-200 text-slate-500') }}">
                                            @if ($item->is_completed)
                                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            @elseif (! $item->is_unlocked)
                                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                </svg>
                                            @else
                                                {{ $mod->sequence }}
                                            @endif
                                        </div>

                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-bold text-slate-400">Module {{ $mod->sequence }}</span>
                                                <span class="text-slate-300">&bull;</span>
                                                <span class="text-xs text-slate-500 font-medium">{{ $mod->estimated_duration ?? '30' }} mins</span>
                                                @if ($mod->is_required)
                                                    <span class="text-[10px] font-semibold text-slate-500 uppercase bg-slate-100 px-1.5 py-0.5 rounded">Required</span>
                                                @endif
                                            </div>

                                            <h4 class="text-base font-bold text-slate-900 mt-1">
                                                {{ $mod->title }}
                                            </h4>

                                            <p class="text-xs text-slate-600 mt-1 line-clamp-2">
                                                {{ $mod->description }}
                                            </p>

                                            <!-- Materials counter -->
                                            <div class="mt-2 flex items-center gap-3 text-xs text-slate-500">
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                    {{ $mod->materials->count() }} Training Resources
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Button -->
                                    <div class="shrink-0 flex items-center">
                                        @if (! $isEnrolled)
                                            <form method="POST" action="{{ route('teacher.workshops.register', $workshop) }}">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 text-xs font-bold transition-colors">
                                                    <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                    </svg>
                                                    Register to Unlock
                                                </button>
                                            </form>
                                        @elseif ($item->is_completed)
                                            <a href="{{ route('teacher.learning.module', [$workshop, $mod]) }}"
                                               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors shadow-2xs">
                                                Review Module
                                            </a>
                                        @elseif ($item->is_unlocked)
                                            <a href="{{ route('teacher.learning.module', [$workshop, $mod]) }}"
                                               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-xs font-bold text-white transition-colors shadow-xs">
                                                {{ $item->is_in_progress ? 'Continue' : 'Start Module' }} &rarr;
                                            </a>
                                        @else
                                            <div class="flex items-center gap-1.5 text-xs text-slate-400 font-medium bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                                                <span>🔒 Locked</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Right Col: Gating Blocks (Assessment & Classroom Packages) -->
            <div class="space-y-6">
                <!-- 1. Assessment Gating Card -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-indigo-700 uppercase tracking-wider">Accreditation Exam</span>
                        @if ($certification)
                            <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                                Passed
                            </span>
                        @elseif ($allRequiredCompleted)
                            <span class="inline-flex items-center gap-1 text-xs font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 px-2 py-0.5 rounded-full">
                                Unlocked
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs font-bold text-rose-700 bg-rose-50 border border-rose-200 px-2 py-0.5 rounded-full">
                                Locked
                            </span>
                        @endif
                    </div>

                    <h4 class="mt-2 text-base font-bold text-slate-900">Final Assessment</h4>
                    <p class="mt-1 text-xs text-slate-500 leading-relaxed">
                        Demonstrate mastery of disaster readiness concepts to receive your certificate and unlock the workshop repository.
                    </p>

                    @if ($assessment)
                        <div class="mt-4 p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs space-y-1.5 text-slate-600">
                            <div class="flex justify-between">
                                <span>Passing Score:</span>
                                <span class="font-bold text-slate-800">{{ $assessment->passing_score }}%</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Time Limit:</span>
                                <span class="font-bold text-slate-800">{{ $assessment->time_limit }} minutes</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Attempts Used:</span>
                                <span class="font-bold text-slate-800">{{ $attemptCount }} / {{ $assessment->max_attempts }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Gating notification -->
                    <div class="mt-4">
                        @if ($certification)
                            <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800">
                                <div class="font-bold">You are certified!</div>
                                <div class="text-[11px] text-emerald-700 mt-0.5">Certificate #{{ $certification->certificate_number }}</div>
                            </div>
                            <a href="{{ route('teacher.assessments.certificate', $workshop) }}" target="_blank"
                               class="mt-3 block w-full text-center py-2 px-4 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shadow-2xs transition-colors">
                                View / Print Certificate
                            </a>
                        @elseif (! $allRequiredCompleted)
                            <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800">
                                <div class="font-bold flex items-center gap-1.5">
                                    <span>🔒 Exam Locked</span>
                                </div>
                                <div class="text-[11px] text-amber-700 mt-1 leading-relaxed">
                                    Finish 100% of required modules above to unlock the final examination.
                                </div>
                            </div>
                            <button type="button" disabled 
                                    class="mt-3 w-full py-2.5 rounded-xl bg-slate-100 text-slate-400 font-bold text-xs cursor-not-allowed border border-slate-200">
                                Exam Locked
                            </button>
                        @elseif ($attemptCount >= ($assessment->max_attempts ?? 3))
                            <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-800">
                                <div class="font-bold">Maximum Attempts Reached</div>
                                <div class="text-[11px] text-rose-700 mt-1">Please contact your DRR trainer for re-evaluation.</div>
                            </div>
                        @else
                            <a href="{{ route('teacher.assessments.show', $workshop) }}"
                               class="block w-full text-center py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs transition-colors">
                                Take Final Assessment &rarr;
                            </a>
                        @endif
                    </div>
                </div>

                <!-- 2. Workshop Repository Card -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-sky-700 uppercase tracking-wider">Workshop Repository</span>
                        @if ($isPackageUnlocked)
                            <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                                Unlocked
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs font-bold text-slate-500 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-full">
                                Locked
                            </span>
                        @endif
                    </div>

                    <h4 class="mt-2 text-base font-bold text-slate-900">Workshop Repository</h4>
                    <p class="mt-1 text-xs text-slate-500 leading-relaxed">
                        Physical worksheets, teacher guides, student drills, and assessment rubrics for your classroom.
                    </p>

                    <div class="mt-4">
                        @if ($isPackageUnlocked)
                            <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 mb-3">
                                <strong>Repository Unlocked!</strong> You can now download workshop materials and submit implementation reports.
                            </div>
                            @if ($package)
                                <a href="{{ route('teacher.packages.show', $package) }}"
                                   class="block w-full text-center py-2 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-2xs transition-colors">
                                    Open Workshop Repository
                                </a>
                            @endif
                        @else
                            <div class="p-3 rounded-xl bg-slate-100 border border-slate-200 text-xs text-slate-600 mb-3">
                                Complete and pass the final assessment to unlock all classroom teaching guides and student manuals.
                            </div>
                            <button type="button" disabled 
                                    class="w-full py-2.5 rounded-xl bg-slate-100 text-slate-400 font-bold text-xs cursor-not-allowed border border-slate-200">
                                🔒 Locked Until Certified
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
