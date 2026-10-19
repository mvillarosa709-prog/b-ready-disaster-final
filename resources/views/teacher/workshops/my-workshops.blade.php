<x-dynamic-component component="layouts.teacher">
    <x-slot name="title">My Workshops</x-slot>
    <x-slot name="header">My Registered Workshops</x-slot>

    <div class="space-y-6" x-data="{ activeTab: 'active' }">
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

        <!-- Tab Navigation Bar -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-2 shadow-xs flex items-center justify-between">
            <div class="flex items-center gap-2">
                <button type="button" 
                        @click="activeTab = 'active'"
                        :class="activeTab === 'active' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all">
                    In Progress ({{ count($activeWorkshops) }})
                </button>
                <button type="button" 
                        @click="activeTab = 'completed'"
                        :class="activeTab === 'completed' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all">
                    Completed & Certified ({{ count($completedWorkshops) }})
                </button>
            </div>

            <a href="{{ route('teacher.workshops.index') }}" 
               class="hidden sm:inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-800 px-3 py-1.5 rounded-lg hover:bg-indigo-50 transition-colors">
                + Browse More
            </a>
        </div>

        <!-- Tab 1: In Progress Workshops -->
        <div x-show="activeTab === 'active'">
            @if (empty($activeWorkshops))
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
                    <div class="mx-auto h-12 w-12 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="mt-3 text-sm font-bold text-slate-900">No active workshops in progress</h3>
                    <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto">
                        You have finished your active workshops or haven't joined one yet.
                    </p>
                    <a href="{{ route('teacher.workshops.index') }}" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition-colors">
                        Discover Workshops
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($activeWorkshops as $item)
                        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col justify-between hover:border-indigo-200 transition-all">
                            <div>
                                <div class="flex items-center justify-between gap-2">
                                    <span class="inline-block text-[11px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-100 rounded-md px-2.5 py-1">
                                        {{ $item->workshop->course->title ?? 'DRR Course' }}
                                    </span>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 border border-amber-200">
                                        In Progress
                                    </span>
                                </div>

                                <h3 class="mt-3 text-base font-bold text-slate-900 line-clamp-2">
                                    <a href="{{ route('teacher.workshops.show', $item->workshop) }}" class="hover:text-indigo-600 transition-colors">
                                        {{ $item->workshop->title }}
                                    </a>
                                </h3>

                                <p class="mt-2 text-xs text-slate-600 line-clamp-2">
                                    {{ $item->workshop->description ?? 'Disaster Preparedness and Risk Reduction curriculum modules.' }}
                                </p>

                                <!-- Progress Bar -->
                                <div class="mt-5 p-3 rounded-xl bg-slate-50 border border-slate-100">
                                    <div class="flex justify-between text-xs font-medium text-slate-600 mb-1.5">
                                        <span>Completion</span>
                                        <span class="font-bold text-slate-900">{{ $item->progress_percentage }}%</span>
                                    </div>
                                    <div class="w-full h-2.5 bg-slate-200/70 rounded-full overflow-hidden">
                                        <div class="h-full bg-indigo-600 rounded-full transition-all duration-500" style="width: {{ $item->progress_percentage }}%"></div>
                                    </div>
                                    <div class="mt-2 text-[11px] text-slate-500 text-right">
                                        {{ $item->completed_modules }} of {{ $item->total_modules }} modules completed
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between gap-2">
                                <a href="{{ route('teacher.workshops.show', $item->workshop) }}"
                                   class="text-xs font-bold text-slate-700 hover:text-indigo-600 px-3 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 transition-colors">
                                    Syllabus
                                </a>

                                @if ($item->next_module)
                                    <a href="{{ route('teacher.learning.module', [$item->workshop, $item->next_module]) }}"
                                       class="inline-flex items-center gap-1.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded-xl transition-colors shadow-xs">
                                        {{ $item->completed_modules === 0 ? 'Start Module ' . $item->next_module->sequence : 'Resume Module ' . $item->next_module->sequence }} &rarr;
                                    </a>
                                @else
                                    <a href="{{ route('teacher.assessments.show', $item->workshop) }}"
                                       class="inline-flex items-center gap-1.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 px-4 py-2 rounded-xl transition-colors shadow-xs">
                                        Take Exam &rarr;
                                    </a>
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
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
                    <div class="mx-auto h-12 w-12 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z"/>
                        </svg>
                    </div>
                    <h3 class="mt-3 text-sm font-bold text-slate-900">No completed workshops yet</h3>
                    <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto">
                        Complete all modules and pass the final exam in your registered workshops to unlock official certifications.
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($completedWorkshops as $item)
                        <div class="bg-white rounded-2xl border border-emerald-200/80 p-5 shadow-xs flex flex-col justify-between hover:shadow-md transition-all">
                            <div>
                                <div class="flex items-center justify-between gap-2">
                                    <span class="inline-block text-[11px] font-bold text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-md px-2.5 py-1">
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

                                <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs space-y-1">
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
                                   class="flex-1 text-center py-2 px-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-xs font-bold text-white transition-colors shadow-2xs">
                                    Certificate
                                </a>
                                @if ($item->workshop->classroomPackage)
                                    <a href="{{ route('teacher.packages.show', $item->workshop->classroomPackage) }}"
                                       class="flex-1 text-center py-2 px-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-xs font-bold text-white transition-colors shadow-2xs">
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
