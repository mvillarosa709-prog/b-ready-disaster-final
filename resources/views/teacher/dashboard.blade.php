<x-dynamic-component component="layouts.teacher">
    <x-slot name="title">Teacher Dashboard Overview</x-slot>

    <!-- Header Banner (Matches Trainer Side) -->
    <div class="bg-white border-b border-slate-200/80 px-4 py-8 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        DRR Teacher Portal
                    </span>
                    <span class="text-xs text-slate-400">Accredited Educator</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2 tracking-tight">
                    Welcome back, {{ auth()->user()->name }}!
                </h1>
                <p class="text-slate-500 text-sm mt-1">
                    Access your registered Disaster Risk Reduction workshops, complete sequential modules, and track classroom implementations.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('teacher.workshops.my') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 shadow-sm transition">
                    <span>My Workshops</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats Grid (Clean, Matches Trainer Dashboard) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 space-y-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Enrolled Workshops -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-emerald-200 transition">
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Enrolled Workshops</span>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-slate-900">{{ $totalEnrolled ?? 0 }}</span>
                    <span class="text-xs text-emerald-600 font-semibold">{{ $totalInProgress ?? 0 }} In Progress</span>
                </div>
            </div>

            <!-- Completed Modules -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-blue-200 transition">
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Modules Completed</span>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-slate-900">{{ $completedModulesCount ?? 0 }}</span>
                    <span class="text-xs text-slate-500 font-medium">Finished units</span>
                </div>
            </div>

            <!-- Certifications -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-amber-200 transition">
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Certifications</span>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-slate-900">{{ $totalCertifications ?? 0 }}</span>
                    <span class="text-xs text-amber-600 font-semibold">{{ $totalCompleted ?? 0 }} Completed</span>
                </div>
            </div>

            <!-- Classroom Impact -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-purple-200 transition">
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Students Reached</span>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-slate-900">{{ number_format($totalStudentsReached ?? 0) }}</span>
                    <span class="text-xs text-emerald-600 font-semibold">{{ $overallClassAverage ?? 0 }}% Class Avg</span>
                </div>
            </div>
        </div>

        <!-- Section: Active Workshops Progress -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Your Active Workshops</h2>
                    <p class="text-xs text-slate-500">Pick up where you left off in your disaster preparedness training.</p>
                </div>
                <a href="{{ route('teacher.workshops.my') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">
                    View all my workshops &rarr;
                </a>
            </div>

            @if ($activeWorkshops->isEmpty())
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center shadow-xs">
                    <h3 class="text-base font-bold text-slate-900">No active workshops yet</h3>
                    <p class="mt-1 text-xs text-slate-500 max-w-md mx-auto">
                        Explore available published disaster preparedness workshops and modules to start your training.
                    </p>
                    <a href="{{ route('teacher.workshops.index') }}" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-xs font-semibold text-white hover:bg-emerald-700 shadow-xs transition">
                        Browse Workshops
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @foreach ($activeWorkshops as $item)
                        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs flex flex-col justify-between hover:border-emerald-200 transition-all">
                            <div>
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <span class="inline-block text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200/70 rounded-md px-2.5 py-0.5">
                                            {{ $item->workshop->title }}
                                        </span>
                                        <h3 class="mt-2.5 text-base font-bold text-slate-900 line-clamp-1">
                                            {{ $item->workshop->course->title ?? $item->workshop->title }}
                                        </h3>
                                    </div>
                                    @if ($item->has_certificate)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800 shrink-0">
                                            Certified
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-800 shrink-0">
                                            In Progress
                                        </span>
                                    @endif
                                </div>

                                <p class="mt-2 text-xs text-slate-500 line-clamp-2">
                                    {{ $item->workshop->description ?? 'Learn comprehensive disaster risk reduction principles and student classroom activities.' }}
                                </p>

                                <!-- Visual Progress Bar -->
                                <div class="mt-4">
                                    <div class="flex justify-between text-xs font-medium text-slate-600 mb-1.5">
                                        <span>Course Progress</span>
                                        <span class="font-bold text-slate-800">{{ $item->progress_percentage }}% ({{ $item->completed_modules }}/{{ $item->total_modules }} modules)</span>
                                    </div>
                                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-emerald-600 rounded-full transition-all duration-500" style="width: {{ $item->progress_percentage }}%"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action button -->
                            <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between">
                                <div class="text-xs text-slate-500">
                                    Trainer: <span class="font-semibold text-slate-700">{{ $item->workshop->trainer->name ?? 'DRR Expert' }}</span>
                                </div>

                                @if ($item->next_module)
                                    <a href="{{ route('teacher.learning.module', [$item->workshop, $item->next_module]) }}"
                                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 px-4 py-2 rounded-xl transition-colors shadow-xs">
                                        Continue Module {{ $item->next_module->sequence }}
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                @else
                                    <a href="{{ route('teacher.workshops.show', $item->workshop) }}"
                                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-4 py-2 rounded-xl transition-colors border border-emerald-200">
                                        View Workshop
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Available Workshops Discovery Section -->
        @if ($availableWorkshops->isNotEmpty())
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Available Workshops to Register For</h2>
                        <p class="text-xs text-slate-500">Expand your disaster preparedness accreditation.</p>
                    </div>
                    <a href="{{ route('teacher.workshops.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                        Browse all &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    @foreach ($availableWorkshops as $av)
                        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col justify-between hover:border-emerald-200 transition">
                            <div>
                                <span class="text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-100 px-2.5 py-0.5 rounded-md inline-block">
                                    {{ $av->title }}
                                </span>
                                <h3 class="mt-2.5 text-sm font-bold text-slate-900 line-clamp-1">{{ $av->course->title ?? $av->title }}</h3>
                                <p class="mt-1 text-xs text-slate-500 line-clamp-2">{{ $av->course->description ?? $av->description }}</p>
                            </div>
                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-[11px] text-slate-500">
                                    Starts {{ $av->start_date ? $av->start_date->format('M d') : 'Flexible' }}
                                </span>
                                <form method="POST" action="{{ route('teacher.workshops.register', $av) }}">
                                    @csrf
                                    <button type="submit" class="px-3.5 py-1.5 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-xs transition-colors">
                                        Enroll Now
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-dynamic-component>
