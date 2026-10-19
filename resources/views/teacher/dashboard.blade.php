<x-dynamic-component component="layouts.teacher">
    <x-slot name="title">Teacher Dashboard</x-slot>
    <x-slot name="header">Teacher Dashboard</x-slot>

    <div class="space-y-8">
        <!-- Hero Welcome Banner -->
        <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-200 p-6 sm:p-8 text-gray-900 shadow-sm mb-8">
            <div class="relative z-10 max-w-2xl">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 border border-indigo-200">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span> DRR Preparedness Active
                </span>
                <h2 class="mt-3 text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-900">
                    Welcome back, {{ auth()->user()->name }}!
                </h2>
                <p class="mt-2 text-sm sm:text-base text-gray-600 leading-relaxed">
                    Access your registered Disaster Risk Reduction workshops, complete sequential modules, earn your certification, and access workshop repositories for your classroom.
                </p>
                <div class="mt-5 flex flex-wrap gap-3">
                    <a href="{{ route('teacher.workshops.my') }}" 
                       class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs sm:text-sm font-bold text-white shadow-sm hover:bg-indigo-700 transition-colors">
                        Continue Learning
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                    <a href="{{ route('teacher.workshops.index') }}" 
                       class="inline-flex items-center gap-2 rounded-xl bg-gray-100 hover:bg-gray-200 px-4 py-2.5 text-xs sm:text-sm font-semibold text-gray-700 transition-colors">
                        Browse Workshops
                    </a>
                    <a href="{{ route('teacher.reports.index') }}" 
                       class="inline-flex items-center gap-2 rounded-xl bg-white hover:bg-gray-50 px-4 py-2.5 text-xs sm:text-sm font-bold text-gray-700 border border-gray-300 shadow-sm transition-colors">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Personal Reports Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Section: Active Workshops Progress -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Your Active Workshops</h3>
                    <p class="text-xs text-slate-500">Pick up where you left off in your disaster preparedness journey.</p>
                </div>
                <a href="{{ route('teacher.workshops.my') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                    View all my workshops &rarr;
                </a>
            </div>

            @if ($activeWorkshops->isEmpty())
                <div class="rounded-xl border border-dashed border-slate-300 bg-white p-8 text-center">
                    <div class="mx-auto h-12 w-12 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h4 class="mt-3 text-sm font-bold text-slate-900">No active workshops yet</h4>
                    <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto">
                        Explore available published disaster preparedness workshops to start training.
                    </p>
                    <a href="{{ route('teacher.workshops.index') }}" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition-colors">
                        Browse Workshops
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @foreach ($activeWorkshops as $item)
                        <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-xs flex flex-col justify-between hover:border-indigo-200 transition-all">
                            <div>
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <span class="inline-block text-[11px] font-semibold text-indigo-700 bg-indigo-50 border border-indigo-100 rounded-md px-2 py-0.5">
                                            {{ $item->workshop->course->title ?? 'DRR Training Course' }}
                                        </span>
                                        <h4 class="mt-2 text-base font-bold text-slate-900 line-clamp-1">
                                            {{ $item->workshop->title }}
                                        </h4>
                                    </div>
                                    @if ($item->has_certificate)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 border border-emerald-200 shrink-0">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Certified
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 border border-amber-200 shrink-0">
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
                                        <div class="h-full bg-indigo-600 rounded-full transition-all duration-500" style="width: {{ $item->progress_percentage }}%"></div>
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
                                       class="inline-flex items-center gap-1.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 px-3.5 py-2 rounded-lg transition-colors shadow-xs">
                                        Continue Module {{ $item->next_module->sequence }}
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                @else
                                    <a href="{{ route('teacher.workshops.show', $item->workshop) }}"
                                       class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 px-3.5 py-2 rounded-lg transition-colors border border-indigo-200">
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

        <!-- Available Workshops Discovery Banner -->
        @if ($availableWorkshops->isNotEmpty())
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Available Workshops to Register For</h3>
                        <p class="text-xs text-slate-500">Expand your disaster preparedness accreditation.</p>
                    </div>
                    <a href="{{ route('teacher.workshops.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                        Browse all &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    @foreach ($availableWorkshops as $av)
                        <div class="bg-white rounded-xl border border-slate-200/80 p-4 shadow-xs flex flex-col justify-between">
                            <div>
                                <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider bg-indigo-50 px-2 py-0.5 rounded">
                                    {{ $av->course->title ?? 'DRR Training' }}
                                </span>
                                <h4 class="mt-2 text-sm font-bold text-slate-900 line-clamp-1">{{ $av->title }}</h4>
                                <p class="mt-1 text-xs text-slate-500 line-clamp-2">{{ $av->description }}</p>
                            </div>
                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-[11px] text-slate-500">
                                    Starts {{ $av->start_date ? $av->start_date->format('M d') : 'Flexible' }}
                                </span>
                                <form method="POST" action="{{ route('teacher.workshops.register', $av) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-2xs transition-colors">
                                        Register for Workshop
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