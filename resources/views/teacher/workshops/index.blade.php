<x-dynamic-component component="layouts.teacher">
    <x-slot name="title">Discover Workshops — B-READY</x-slot>

    <!-- Header Banner (Matches Trainer Side) -->
    <div class="bg-white border-b border-slate-200/80 px-4 py-8 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        Workshop Discovery
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2 tracking-tight">
                    Discover Workshops
                </h1>
                <p class="text-slate-500 text-sm mt-1">
                    Browse published DRR workshops and search training modules published by accredited trainers.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('teacher.workshops.my') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 shadow-xs transition">
                    My Workshops
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 space-y-8">

        <!-- Search & Filter Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="w-full sm:w-auto">
                <h2 class="text-base font-bold text-slate-900">Available Workshops</h2>
                <p class="text-xs text-slate-500">Browse published workshops and modules from Disaster Risk Reduction trainers.</p>
            </div>

            <form method="GET" action="{{ route('teacher.workshops.index') }}" class="w-full sm:w-96 flex items-center gap-2">
                <div class="relative w-full">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search workshops, courses, or modules..."
                           class="block w-full rounded-xl border border-slate-300 bg-white pl-9 pr-4 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:border-emerald-500 focus:outline-hidden focus:ring-1 focus:ring-emerald-500">
                </div>
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shrink-0 transition-colors shadow-xs">
                    Search
                </button>
                @if (request('search'))
                    <a href="{{ route('teacher.workshops.index') }}" class="px-3 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold shrink-0 transition-colors">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Workshops Grid -->
        @if ($workshops->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center shadow-xs">
                <h3 class="text-base font-bold text-slate-900">No published workshops found</h3>
                <p class="mt-1 text-xs text-slate-500 max-w-md mx-auto">
                    {{ request('search') ? 'No workshops or modules matched your search keyword. Try another term.' : 'There are currently no active published workshops available for registration.' }}
                </p>
                @if (request('search'))
                    <a href="{{ route('teacher.workshops.index') }}" class="mt-4 inline-flex items-center px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700 transition">
                        Reset Search
                    </a>
                @endif
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($workshops as $workshop)
                    @php
                        $isEnrolled = in_array($workshop->id, $enrolledWorkshopIds);
                        $deadlinePassed = $workshop->registration_deadline && now()->isAfter($workshop->registration_deadline);
                        $modules = $workshop->course ? $workshop->course->modules->sortBy('sequence') : collect();
                        $modulesCount = $modules->count();
                    @endphp

                    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs flex flex-col justify-between hover:shadow-md hover:border-emerald-200 transition-all">
                        <div>
                            <!-- Header tags -->
                            <div class="flex items-start justify-between gap-2">
                                <span class="inline-block text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200/70 rounded-md px-2.5 py-1">
                                    {{ $workshop->title }}
                                </span>
                                @if ($isEnrolled)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800 shrink-0">
                                        Registered
                                    </span>
                                @elseif ($deadlinePassed)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-semibold text-rose-800 shrink-0">
                                        Registration Closed
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-700 shrink-0">
                                        Open
                                    </span>
                                @endif
                            </div>

                            <!-- Course Title & Description (Matches Trainer Course Management) -->
                            <h3 class="mt-3 text-base font-bold text-slate-900 line-clamp-2">
                                <a href="{{ route('teacher.workshops.show', $workshop) }}" class="hover:text-emerald-700 transition-colors">
                                    {{ $workshop->course->title ?? $workshop->title }}
                                </a>
                            </h3>
                            <p class="mt-2 text-xs text-slate-600 line-clamp-2 leading-relaxed">
                                {{ $workshop->course->description ?? ($workshop->description ?? 'Disaster Risk Reduction and Management training for educators.') }}
                            </p>

                            <!-- Included Modules Preview -->
                            @if ($modules->isNotEmpty())
                                <div class="mt-4 pt-3 border-t border-slate-100">
                                    <span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                                        Included Modules ({{ $modulesCount }})
                                    </span>
                                    <ul class="space-y-1">
                                        @foreach ($modules->take(3) as $mod)
                                            <li class="text-xs text-slate-700 flex items-center gap-1.5 truncate">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                                <span class="truncate">{{ $mod->sequence }}. {{ $mod->title }}</span>
                                            </li>
                                        @endforeach
                                        @if ($modulesCount > 3)
                                            <li class="text-[11px] text-emerald-700 font-semibold pl-3">
                                                +{{ $modulesCount - 3 }} more module(s)
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            @endif

                            <!-- Trainer details -->
                            <div class="mt-4 flex items-center gap-2.5">
                                <div class="h-7 w-7 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-xs">
                                    {{ strtoupper(substr($workshop->trainer->name ?? 'T', 0, 1)) }}
                                </div>
                                <div class="text-xs">
                                    <span class="text-slate-400">Trainer:</span>
                                    <span class="font-semibold text-slate-700">{{ $workshop->trainer->name ?? 'DRR Specialist' }}</span>
                                </div>
                            </div>

                            <!-- Timeline Specs -->
                            <div class="mt-4 pt-3 border-t border-slate-100 grid grid-cols-2 gap-2 text-[11px] text-slate-500">
                                <div>
                                    <span class="block text-slate-400 font-medium">Schedule</span>
                                    <span class="font-bold text-slate-700">
                                        {{ $workshop->start_date ? $workshop->start_date->format('M d') : 'Open' }} - {{ $workshop->end_date ? $workshop->end_date->format('M d, Y') : 'Ongoing' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 font-medium">Deadline</span>
                                    <span class="font-bold {{ $deadlinePassed ? 'text-rose-600' : 'text-slate-700' }}">
                                        {{ $workshop->registration_deadline ? $workshop->registration_deadline->format('M d, Y') : 'Rolling' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Action button footer -->
                        <div class="mt-5 pt-4 border-t border-slate-100 flex items-center gap-2">
                            <a href="{{ route('teacher.workshops.show', $workshop) }}"
                               class="flex-1 text-center py-2 px-3 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                                View Details
                            </a>

                            @if ($isEnrolled)
                                <a href="{{ route('teacher.workshops.show', $workshop) }}"
                                   class="flex-1 text-center py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-xs font-semibold text-white transition-colors shadow-xs">
                                    Go to Course &rarr;
                                </a>
                            @elseif ($deadlinePassed)
                                <button type="button" disabled
                                        class="flex-1 py-2 px-3 rounded-xl bg-slate-100 text-xs font-semibold text-slate-400 cursor-not-allowed border border-slate-200">
                                    Registration Closed
                                </button>
                            @else
                                <form method="POST" action="{{ route('teacher.workshops.register', $workshop) }}" class="flex-1">
                                    @csrf
                                    <button type="submit"
                                            class="w-full py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-xs font-semibold text-white transition-colors shadow-xs">
                                        Enroll Now
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $workshops->links() }}
            </div>
        @endif

    </div>
</x-dynamic-component>
