<x-layouts.trainer>
    <x-slot name="title">Trainer Dashboard Overview</x-slot>

    <!-- Header Banner -->
    <div class="bg-white border-b border-slate-200/80">
        <div class="w-full px-4 py-7 sm:px-6 lg:px-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        DRR Trainer Portal
                    </span>
                    <span class="text-xs font-medium text-slate-400">Overview &amp; Analytics</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2 tracking-tight">
                    Welcome back, {{ auth()->user()->name }}!
                </h1>
                <p class="text-slate-500 text-sm mt-1">
                    Manage disaster preparedness courses, module quizzes, scheduled workshops, and teacher training outcomes.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <a href="{{ route('trainer.workshops.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-50 hover:border-slate-300 shadow-xs transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z"/>
                    </svg>
                    Schedule Workshop
                </a>
                <a href="{{ route('trainer.courses.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Course
                </a>
            </div>
        </div>
    </div>

    <!-- Main Dashboard Workspace -->
    <div class="w-full px-4 sm:px-6 lg:px-8 mt-6 space-y-6">
        <!-- Quick Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Courses Stat -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-emerald-200 transition flex flex-col justify-between">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Courses</span>
                        <div class="mt-2 text-3xl font-extrabold text-slate-900">{{ $courseCount ?? 0 }}</div>
                    </div>
                    <div class="h-10 w-10 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="inline-flex items-center gap-1 font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">
                        {{ $publishedCount ?? 0 }} Published
                    </span>
                    <span class="text-slate-400 font-medium">{{ $draftCount ?? 0 }} Drafts</span>
                </div>
            </div>

            <!-- Modules Stat -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-blue-200 transition flex flex-col justify-between">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Learning Modules</span>
                        <div class="mt-2 text-3xl font-extrabold text-slate-900">{{ $moduleCount ?? 0 }}</div>
                    </div>
                    <div class="h-10 w-10 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="inline-flex items-center gap-1 font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md">
                        Sequential units
                    </span>
                    <span class="text-slate-400 font-medium">Curriculum content</span>
                </div>
            </div>

            <!-- Module Quizzes Stat -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-amber-200 transition flex flex-col justify-between">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Module Quizzes</span>
                        <div class="mt-2 text-3xl font-extrabold text-slate-900">{{ $quizCount ?? 0 }}</div>
                    </div>
                    <div class="h-10 w-10 rounded-xl bg-amber-50 border border-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="inline-flex items-center gap-1 font-semibold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md">
                        Across your modules
                    </span>
                    <span class="text-slate-400 font-medium">{{ $workshopCount ?? 0 }} Workshops</span>
                </div>
            </div>

            <!-- Monitoring Stat -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-purple-200 transition flex flex-col justify-between">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Teacher Submissions</span>
                        <div class="mt-2 text-3xl font-extrabold text-slate-900">{{ $submissionCount ?? 0 }}</div>
                    </div>
                    <div class="h-10 w-10 rounded-xl bg-purple-50 border border-purple-100 text-purple-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="inline-flex items-center gap-1 font-semibold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-md">
                        Tracked records
                    </span>
                    <a href="{{ route('trainer.reports') }}" class="text-slate-500 hover:text-emerald-600 font-semibold">View reports &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Two-Column Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            <!-- Left Main Column (2/3 width on desktop) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Main Courses Table Preview -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between gap-4">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Training Courses Overview</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Courses and sequential modules designed for teachers.</p>
                        </div>
                        <a href="{{ route('trainer.courses.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 inline-flex items-center gap-1 shrink-0">
                            Manage Courses
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    @if(isset($courses) && $courses->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-slate-50/80 text-slate-500 uppercase text-xs font-semibold">
                                    <tr>
                                        <th class="px-6 py-3.5 text-left">Course Title</th>
                                        <th class="px-6 py-3.5 text-left">Duration</th>
                                        <th class="px-6 py-3.5 text-left">Modules</th>
                                        <th class="px-6 py-3.5 text-left">Status</th>
                                        <th class="px-6 py-3.5 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($courses->take(5) as $course)
                                        <tr class="hover:bg-slate-50/60 transition">
                                            <td class="px-6 py-4">
                                                <div class="font-bold text-slate-900">{{ $course->title }}</div>
                                                <div class="text-xs text-slate-500 line-clamp-1 mt-0.5">{{ $course->description }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-slate-600 font-medium whitespace-nowrap">
                                                {{ $course->estimated_duration }} mins
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                                    {{ $course->modules->count() }} modules
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($course->status === 'published')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                                        Published
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                                        {{ ucfirst($course->status) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                                <a href="{{ route('trainer.courses.show', $course->id) }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                                                    View Modules &rarr;
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-10 text-center">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-600 mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                            <p class="text-sm font-bold text-slate-800">No training courses created yet.</p>
                            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Start by authoring your first disaster preparedness course and adding sequential learning modules.</p>
                            <div class="mt-4">
                                <a href="{{ route('trainer.courses.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700 shadow-xs transition">
                                    Create First Course
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Workshop Schedule Preview -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between gap-4">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Recent Workshop Sessions</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Scheduled teacher training sessions linked to your courses.</p>
                        </div>
                        <a href="{{ route('trainer.workshops.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 inline-flex items-center gap-1 shrink-0">
                            Manage Workshops
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    @if(isset($workshops) && $workshops->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-slate-50/80 text-slate-500 uppercase text-xs font-semibold">
                                    <tr>
                                        <th class="px-6 py-3.5 text-left">Workshop</th>
                                        <th class="px-6 py-3.5 text-left">Schedule</th>
                                        <th class="px-6 py-3.5 text-left">Teachers</th>
                                        <th class="px-6 py-3.5 text-left">Status</th>
                                        <th class="px-6 py-3.5 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($workshops as $workshop)
                                        <tr class="hover:bg-slate-50/60 transition">
                                            <td class="px-6 py-4">
                                                <div class="font-bold text-slate-900">{{ $workshop->title }}</div>
                                                <div class="text-xs text-slate-500 line-clamp-1 mt-0.5">{{ $workshop->course->title ?? 'Course' }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-xs text-slate-600 whitespace-nowrap">
                                                {{ $workshop->start_date->format('M d, Y') }} – {{ $workshop->end_date->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 text-slate-700 font-semibold whitespace-nowrap">
                                                {{ $workshop->workshop_teachers_count ?? 0 }} enrolled
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $workshop->status === 'published' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                                    {{ ucfirst($workshop->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                                <a href="{{ route('trainer.workshops.show', $workshop) }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                                                    Details &rarr;
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-8 text-center">
                            <p class="text-sm font-bold text-slate-800">No workshops scheduled yet.</p>
                            <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">Once you create a course, schedule a workshop session so teachers can register and complete modules.</p>
                            <div class="mt-4">
                                <a href="{{ route('trainer.workshops.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-semibold transition">
                                    + Schedule Workshop
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column: Workflow Guide (1/3 width on desktop) -->
            <div class="space-y-6">
                <!-- Trainer Workflow Checklist Card -->
                <div class="rounded-2xl bg-gradient-to-br from-emerald-50 to-slate-100 p-6 border border-emerald-200/70">
                    <div class="flex items-center gap-2 text-emerald-900 font-bold text-xs uppercase tracking-wider">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Training Lifecycle Guide
                    </div>
                    <ol class="mt-3.5 space-y-2.5 text-xs text-slate-700">
                        <li class="flex items-start gap-2.5">
                            <span class="h-5 w-5 rounded-full bg-emerald-600 text-white font-bold text-[11px] flex items-center justify-center shrink-0 mt-0.5">1</span>
                            <span><strong>Create Course:</strong> Define DRR learning objectives and upload sequential module materials.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="h-5 w-5 rounded-full bg-emerald-600 text-white font-bold text-[11px] flex items-center justify-center shrink-0 mt-0.5">2</span>
                            <span><strong>Add Module Quizzes:</strong> Build knowledge checks and publish the course.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="h-5 w-5 rounded-full bg-emerald-600 text-white font-bold text-[11px] flex items-center justify-center shrink-0 mt-0.5">3</span>
                            <span><strong>Publish Workshop:</strong> Schedule training dates and attach classroom packages for teachers.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="h-5 w-5 rounded-full bg-emerald-600 text-white font-bold text-[11px] flex items-center justify-center shrink-0 mt-0.5">4</span>
                            <span><strong>Monitor &amp; Track:</strong> Review auto-generated teacher certifications and monitor classroom student drills.</span>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</x-layouts.trainer>
