<x-dynamic-component component="layouts.teacher">
    <x-slot name="title">Discover Workshops</x-slot>
    <x-slot name="header">Discover & Register for DRR Workshops</x-slot>

    <div class="space-y-6">

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

    <!-- Top Search & Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="w-full sm:w-auto">
            <h2 class="text-lg font-bold text-slate-900">Available Workshops</h2>
            <p class="text-xs text-slate-500">Find training workshops designed by Disaster Risk Reduction experts.</p>
        </div>
        <!-- Search Form -->
        <form method="GET" action="{{ route('teacher.workshops.index') }}" class="w-full sm:w-80 flex items-center gap-2">
                <div class="relative w-full">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search workshops or topics..."
                           class="block w-full rounded-xl border border-slate-200 bg-slate-50/60 pl-9 pr-4 py-2 text-xs text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:outline-hidden focus:ring-1 focus:ring-indigo-500">
                </div>
                <button type="submit" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shrink-0 transition-colors shadow-2xs">
                    Search
                </button>
                @if (request('search'))
                    <a href="{{ route('teacher.workshops.index') }}" class="px-2.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold shrink-0 transition-colors">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Workshops Grid -->
        @if ($workshops->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
                <div class="mx-auto h-12 w-12 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="mt-3 text-sm font-bold text-slate-900">No workshops found</h3>
                <p class="mt-1 text-xs text-slate-500">
                    {{ request('search') ? 'No results matched your search keyword. Try a different query.' : 'There are currently no active published workshops available for registration.' }}
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($workshops as $workshop)
                    @php
                        $isEnrolled = in_array($workshop->id, $enrolledWorkshopIds);
                        $deadlinePassed = $workshop->registration_deadline && now()->isAfter($workshop->registration_deadline);
                        $modulesCount = $workshop->course ? $workshop->course->modules->count() : 0;
                    @endphp

                    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col justify-between hover:shadow-md hover:border-indigo-200 transition-all">
                        <div>
                            <!-- Header tags -->
                            <div class="flex items-start justify-between gap-2">
                                <span class="inline-block text-[11px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-100/80 rounded-md px-2.5 py-1">
                                    {{ $workshop->course->title ?? 'DRR Foundation' }}
                                </span>
                                @if ($isEnrolled)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 border border-emerald-200">
                                         <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                         </svg>
                                         Registered
                                     </span>
                                 @elseif ($deadlinePassed)
                                     <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700 border border-rose-200">
                                         Registration Closed
                                     </span>
                                 @else
                                     <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 border border-indigo-200">
                                         Open for Registration
                                     </span>
                                 @endif
                            </div>

                            <!-- Title & Description -->
                            <h3 class="mt-3 text-base font-bold text-slate-900 line-clamp-2">
                                <a href="{{ route('teacher.workshops.show', $workshop) }}" class="hover:text-indigo-600 transition-colors">
                                    {{ $workshop->title }}
                                </a>
                            </h3>
                            <p class="mt-2 text-xs text-slate-600 line-clamp-3 leading-relaxed">
                                {{ $workshop->description ?? ($workshop->course->description ?? 'Disaster Risk Reduction and Management training for educators and school coordinators.') }}
                            </p>

                            <!-- Trainer details -->
                            <div class="mt-4 flex items-center gap-2.5">
                                <div class="h-7 w-7 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-xs">
                                    {{ substr($workshop->trainer->name ?? 'T', 0, 1) }}
                                </div>
                                <div class="text-xs">
                                    <span class="text-slate-400">Trainer:</span>
                                    <span class="font-semibold text-slate-700">{{ $workshop->trainer->name ?? 'DRR Specialist' }}</span>
                                </div>
                            </div>

                            <!-- Timeline & Module Specs -->
                            <div class="mt-4 pt-3 border-t border-slate-100 grid grid-cols-2 gap-2 text-[11px] text-slate-500">
                                <div>
                                    <span class="block text-slate-400 font-medium">Modules</span>
                                    <span class="font-bold text-slate-700">{{ $modulesCount }} Learning Modules</span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 font-medium">Duration</span>
                                    <span class="font-bold text-slate-700">{{ $workshop->course->estimated_duration ?? '4' }} Hours total</span>
                                </div>
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
                               class="flex-1 text-center py-2 px-3 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors">
                                View Details
                            </a>

                            @if ($isEnrolled)
                                <a href="{{ route('teacher.workshops.show', $workshop) }}"
                                   class="flex-1 text-center py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-xs font-bold text-white transition-colors shadow-2xs">
                                    Go to Course &rarr;
                                </a>
                            @elseif ($deadlinePassed)
                                <button type="button" disabled 
                                        class="flex-1 py-2 px-3 rounded-xl bg-slate-100 text-xs font-bold text-slate-400 cursor-not-allowed border border-slate-200">
                                    Registration Closed
                                </button>
                            @else
                                <form method="POST" action="{{ route('teacher.workshops.register', $workshop) }}" class="flex-1">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full py-2 px-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-xs font-bold text-white transition-colors shadow-xs">
                                        Register for Workshop
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
