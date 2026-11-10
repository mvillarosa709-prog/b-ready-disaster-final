<x-layouts.trainer>
    <x-slot name="title">Trainer Dashboard Overview</x-slot>

    <!-- Header Banner -->
    <div class="bg-white border-b border-slate-200/80 px-4 py-8 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        DRR Trainer Portal
                    </span>
                    <span class="text-xs text-slate-400">Accredited Facilitator</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2 tracking-tight">
                    Welcome back, {{ auth()->user()->name }}!
                </h1>
                <p class="text-slate-500 text-sm mt-1">
                    Manage disaster preparedness courses, module quizzes, and teacher training outcomes.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('trainer.courses.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Course
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats Grid (Clean, Icon-free) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Courses Stat -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-emerald-200 transition">
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Courses</span>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-slate-900">{{ $courseCount ?? 0 }}</span>
                    <span class="text-xs text-emerald-600 font-semibold">{{ $publishedCount ?? 0 }} Published</span>
                </div>
            </div>

            <!-- Modules Stat -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-blue-200 transition">
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Learning Modules</span>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-slate-900">{{ $moduleCount ?? 0 }}</span>
                    <span class="text-xs text-slate-500 font-medium">Sequential units</span>
                </div>
            </div>

            <!-- Module Quizzes Stat -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-amber-200 transition">
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Module Quizzes</span>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-slate-900">{{ $quizCount ?? 0 }}</span>
                    <span class="text-xs text-amber-600 font-semibold">Across your modules</span>
                </div>
            </div>

            <!-- Monitoring Stat -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-purple-200 transition">
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Teacher Submissions</span>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-slate-900">{{ $submissionCount ?? 0 }}</span>
                    <span class="text-xs text-purple-600 font-semibold">Tracked records</span>
                </div>
            </div>
        </div>

        <!-- Main Courses Table Preview -->
        <div class="mt-8 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Training Courses Overview</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Courses and modules designed for teachers.</p>
                </div>
                <a href="{{ route('trainer.courses.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
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
                            @foreach($courses as $course)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-900">{{ $course->title }}</div>
                                        <div class="text-xs text-slate-500 line-clamp-1 mt-0.5">{{ $course->description }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 font-medium">
                                        {{ $course->estimated_duration }} mins
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                            {{ $course->modules->count() }} modules
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
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
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('trainer.courses.show', $course->id) }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700">
                                            View Modules &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-slate-100 text-slate-400 mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <p class="text-sm font-semibold text-slate-700">No training courses created yet.</p>
                    <p class="text-xs text-slate-400 mt-1">Start by authoring your first disaster preparedness course.</p>
                    <div class="mt-4">
                        <a href="{{ route('trainer.courses.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700 shadow-xs">
                            Create First Course
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.trainer>