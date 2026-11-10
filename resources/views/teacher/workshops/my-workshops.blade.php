<x-dynamic-component component="layouts.teacher">
    <x-slot name="title">My Workshops — B-READY</x-slot>

    <!-- Header Banner (Matches Trainer Side) -->
    <div class="bg-white border-b border-slate-200/80 px-4 py-8 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        My Learning
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2 tracking-tight">
                    My Workshops
                </h1>
                <p class="text-slate-500 text-sm mt-1">
                    Track your active DRR workshops, complete sequential modules, and access earned certifications.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('teacher.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 shadow-xs transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Dashboard
                </a>
                <a href="{{ route('teacher.workshops.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 shadow-sm transition">
                    + Browse More Workshops
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 space-y-6" x-data="{ activeTab: 'active' }">

        <!-- Tab Navigation Bar -->
        <div class="flex items-center justify-between border-b border-slate-200">
            <div class="flex items-center gap-6">
                <button type="button"
                        @click="activeTab = 'active'"
                        :class="activeTab === 'active' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500 hover:text-slate-700'"
                        class="pb-3.5 text-sm font-semibold border-b-2 transition flex items-center gap-2">
                    <span>In Progress</span>
                    <span class="px-2 py-0.5 text-xs rounded-full bg-amber-100 text-amber-800">{{ count($activeWorkshops) }}</span>
                </button>
                <button type="button"
                        @click="activeTab = 'completed'"
                        :class="activeTab === 'completed' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500 hover:text-slate-700'"
                        class="pb-3.5 text-sm font-semibold border-b-2 transition flex items-center gap-2">
                    <span>Completed &amp; Certified</span>
                    <span class="px-2 py-0.5 text-xs rounded-full bg-emerald-100 text-emerald-800">{{ count($completedWorkshops) }}</span>
                </button>
            </div>
        </div>

        <!-- Tab 1: In Progress Workshops -->
        <div x-show="activeTab === 'active'">
            @if (empty($activeWorkshops))
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center shadow-xs">
                    <h3 class="text-base font-bold text-slate-900">No active workshops in progress</h3>
                    <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto">
                        You have finished your active workshops or haven't joined one yet.
                    </p>
                    <a href="{{ route('teacher.workshops.index') }}" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-xs font-semibold text-white hover:bg-emerald-700 shadow-xs transition">
                        Discover Workshops
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($activeWorkshops as $item)
                        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs flex flex-col justify-between hover:border-emerald-200 transition-all">
                            <div>
                                <div class="flex items-center justify-between gap-2">
                                    <span class="inline-block text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200/70 rounded-md px-2.5 py-0.5">
                                        {{ $item->workshop->course->title ?? 'DRR Course' }}
                                    </span>
                                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-800">
                                        In Progress
                                    </span>
                                </div>

                                <h3 class="mt-3 text-base font-bold text-slate-900 line-clamp-2">
                                    <a href="{{ route('teacher.workshops.show', $item->workshop) }}" class="hover:text-emerald-700 transition-colors">
                                        {{ $item->workshop->title }}
                                    </a>
                                </h3>

                                <p class="mt-2 text-xs text-slate-600 line-clamp-2">
                                    {{ $item->workshop->description ?? 'Disaster Preparedness and Risk Reduction curriculum modules.' }}
                                </p>

                                <!-- Progress Bar -->
                                <div class="mt-5 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                    <div class="flex justify-between text-xs font-medium text-slate-600 mb-1.5">
                                        <span>Completion</span>
                                        <span class="font-bold text-slate-900">{{ $item->progress_percentage }}%</span>
                                    </div>
                                    <div class="w-full h-2.5 bg-slate-200/70 rounded-full overflow-hidden">
                                        <div class="h-full bg-emerald-600 rounded-full transition-all duration-500" style="width: {{ $item->progress_percentage }}%"></div>
                                    </div>
                                    <div class="mt-2 text-[11px] text-slate-500 text-right">
                                        {{ $item->completed_modules }} of {{ $item->total_modules }} modules completed
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between gap-2">
                                <a href="{{ route('teacher.workshops.show', $item->workshop) }}"
                                   class="text-xs font-semibold text-slate-700 hover:text-emerald-700 px-3 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 transition-colors">
                                    Syllabus
                                </a>

                                @if ($item->next_module)
                                    <a href="{{ route('teacher.learning.module', [$item->workshop, $item->next_module]) }}"
                                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 px-4 py-2 rounded-xl transition-colors shadow-xs">
                                        {{ $item->completed_modules === 0 ? 'Start Module ' . $item->next_module->sequence : 'Resume Module ' . $item->next_module->sequence }} &rarr;
                                    </a>
                                @else
                                    @if ($item->workshop->assessment?->module_id)
                                        <a href="{{ route('teacher.learning.module', [$item->workshop, $item->workshop->assessment->module_id]) }}"
                                           class="inline-flex items-center gap-1.5 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 px-4 py-2 rounded-xl transition-colors shadow-xs">
                                            Continue to Module Quiz &rarr;
                                        </a>
                                    @else
                                        <a href="{{ route('teacher.workshops.show', $item->workshop) }}"
                                           class="inline-flex items-center gap-1.5 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 px-4 py-2 rounded-xl transition-colors shadow-xs">
                                            View Modules &rarr;
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Tab 2: Completed & Certified Workshops -->
        <div x-show="activeTab === 'completed'" style="display: none;">
            @if (empty($completedWorkshops))
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center shadow-xs">
                    <h3 class="text-base font-bold text-slate-900">No completed workshops yet</h3>
                    <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto">
                        Complete all modules and pass the module quizzes in your registered workshops to unlock official certifications.
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($completedWorkshops as $item)
                        <div class="bg-white rounded-2xl border border-emerald-200/80 p-6 shadow-xs flex flex-col justify-between hover:shadow-md transition-all">
                            <div>
                                <div class="flex items-center justify-between gap-2">
                                    <span class="inline-block text-[11px] font-semibold text-emerald-800 bg-emerald-100 border border-emerald-200 rounded-md px-2.5 py-0.5">
                                        Certified
                                    </span>
                                    <span class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        100% Complete
                                    </span>
                                </div>

                                <h3 class="mt-3 text-base font-bold text-slate-900 line-clamp-2">
                                    {{ $item->workshop->title }}
                                </h3>

                                <div class="mt-3 p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs space-y-1">
                                    <div class="text-slate-500">
                                        Credential: <span class="font-bold text-slate-800">{{ $item->certification->certificate_number ?? 'CERT-VALIDATED' }}</span>
                                    </div>
                                    <div class="text-slate-500">
                                        Issued: <span class="font-medium text-slate-700">{{ $item->certification ? $item->certification->certified_at->format('F d, Y') : 'Certified' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5 pt-4 border-t border-slate-100 flex items-center gap-2">
                                <a href="{{ route('teacher.assessments.certificate', $item->workshop) }}" target="_blank"
                                   class="flex-1 text-center py-2 px-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-xs font-semibold text-white transition-colors shadow-xs">
                                    Certificate
                                </a>
                                @if ($item->workshop->classroomPackage)
                                    <a href="{{ route('teacher.packages.show', $item->workshop->classroomPackage) }}"
                                       class="flex-1 text-center py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-xs font-semibold text-white transition-colors shadow-xs">
                                        Workshop Repository
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-dynamic-component>
