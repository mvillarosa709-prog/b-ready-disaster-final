<x-layouts.trainer>
    <x-slot name="title">{{ $workshop->title }} — B-READY</x-slot>

    <!-- Header Banner -->
    <div class="bg-white border-b border-slate-200/80 px-4 py-6 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2">
                    <a href="{{ route('trainer.workshops.index') }}" class="hover:text-emerald-600 transition font-medium">
                        Workshop Management
                    </a>
                    <span>/</span>
                    <span class="text-slate-800 font-semibold truncate max-w-xs">{{ $workshop->title }}</span>
                </nav>

                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
                        {{ $workshop->title }}
                    </h1>
                    @if ($workshop->status === 'published')
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                            Published
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            Draft Mode
                        </span>
                    @endif
                </div>
                <p class="text-sm text-slate-500 mt-1 max-w-2xl">
                    {{ $workshop->description ?: 'No workshop description provided yet.' }}
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2.5">
                <a
                    href="{{ route('trainer.workshops.index') }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-400 transition shadow-xs"
                >
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Workshops
                </a>

                <a
                    href="{{ route('trainer.workshops.edit', $workshop) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 shadow-xs transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit &amp; Publish Settings
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-900 flex items-center gap-3 shadow-xs">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Overview Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Workshop Schedule</span>
                <div class="mt-2 text-base font-bold text-slate-900">
                    {{ $workshop->start_date->format('M d, Y') }} – {{ $workshop->end_date->format('M d, Y') }}
                </div>
                <div class="mt-1 text-xs text-slate-500">
                    Registration closes {{ $workshop->registration_deadline->format('M d, Y g:i A') }}
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Enrolled Teachers</span>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-slate-900">{{ $workshop->workshopTeachers->count() }}</span>
                    <span class="text-xs font-medium text-slate-500">Participants joined</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Course Curriculum</span>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-slate-900">{{ $workshop->course?->modules?->count() ?? 0 }}</span>
                    <span class="text-xs font-medium text-emerald-700 font-semibold">Included modules</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

            <!-- Left 2 cols: Course Modules & Teacher Roster -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Course & Modules Section -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Attached Course &amp; Modules</h2>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Curriculum: <span class="font-semibold text-slate-700">{{ $workshop->course?->title ?? 'Unassigned' }}</span>
                                ({{ ucfirst($workshop->course?->status ?? 'draft') }})
                            </p>
                        </div>
                        @if ($workshop->course)
                            <a href="{{ route('trainer.courses.show', $workshop->course->id) }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                                Manage Course Modules →
                            </a>
                        @endif
                    </div>

                    @if ($workshop->course && $workshop->course->modules->isNotEmpty())
                        <div class="divide-y divide-slate-100">
                            @foreach ($workshop->course->modules->sortBy('sequence') as $module)
                                <div class="px-6 py-4 flex items-center justify-between gap-4 hover:bg-slate-50/60 transition">
                                    <div class="flex items-center gap-3.5">
                                        <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0">
                                            {{ $module->sequence }}
                                        </span>
                                        <div>
                                            <div class="text-sm font-semibold text-slate-900">{{ $module->title }}</div>
                                            <div class="text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($module->description, 80) }}</div>
                                        </div>
                                    </div>
                                    <div>
                                        @if ($module->quiz && $module->quiz->status === 'published')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                                Quiz Ready
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                                Quiz Pending
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 text-center text-sm text-slate-500">
                            No modules found in the attached course yet. Add modules inside Course Management.
                        </div>
                    @endif
                </div>

                <!-- Enrolled Teachers Roster -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Enrolled Teachers</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Teachers who registered for this workshop session.</p>
                        </div>
                    </div>

                    @if ($workshop->workshopTeachers->isEmpty())
                        <div class="p-8 text-center">
                            <p class="text-sm font-semibold text-slate-700">No teachers enrolled yet</p>
                            <p class="text-xs text-slate-500 mt-1">
                                @if ($workshop->status === 'draft')
                                    This workshop is currently a Draft. Publish it in Edit Settings so teachers can enroll.
                                @else
                                    Teachers can join this workshop from the Teacher Portal until {{ $workshop->registration_deadline->format('M d, Y') }}.
                                @endif
                            </p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                                <thead class="bg-slate-50/80 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    <tr>
                                        <th class="px-6 py-3">Teacher</th>
                                        <th class="px-6 py-3">Status</th>
                                        <th class="px-6 py-3">Enrolled Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($workshop->workshopTeachers as $enrollment)
                                        <tr class="hover:bg-slate-50/60">
                                            <td class="px-6 py-3.5">
                                                <div class="font-semibold text-slate-900">{{ $enrollment->teacher?->name ?? 'Teacher' }}</div>
                                                <div class="text-xs text-slate-500">{{ $enrollment->teacher?->email }}</div>
                                            </td>
                                            <td class="px-6 py-3.5">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 capitalize">
                                                    {{ $enrollment->status ?? 'enrolled' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-3.5 text-xs text-slate-600">
                                                {{ $enrollment->joined_at?->format('M d, Y') ?? $enrollment->created_at?->format('M d, Y') ?? '—' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

            </div>

            <!-- Right 1 col: Readiness & Quick Edit -->
            <div class="space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
                    <h3 class="text-sm font-bold text-slate-900">Publication Readiness</h3>

                    @if ($workshop->status === 'published')
                        <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-900">
                            <p class="font-bold">Workshop is Live</p>
                            <p class="mt-1">Teachers can view and register for this workshop before the deadline.</p>
                        </div>
                    @elseif ($canPublish)
                        <div class="p-3.5 rounded-xl bg-blue-50 border border-blue-200 text-xs text-blue-900">
                            <p class="font-bold">Ready to Publish!</p>
                            <p class="mt-1">The attached course and all module quizzes are published. Click <strong>Edit &amp; Publish Settings</strong> to go live.</p>
                        </div>
                    @else
                        <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900">
                            <p class="font-bold">Draft — Course Incomplete</p>
                            <p class="mt-1">Publish the attached course and ensure every module has a published quiz before publishing this workshop.</p>
                        </div>
                    @endif

                    <div class="pt-2 border-t border-slate-100 flex flex-col gap-2">
                        <a href="{{ route('trainer.workshops.edit', $workshop) }}" class="w-full text-center px-4 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800 transition">
                            Edit Workshop &amp; Change Status
                        </a>
                        @if ($workshop->course)
                            <a href="{{ route('trainer.courses.show', $workshop->course->id) }}" class="w-full text-center px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition">
                                Go to Course Content
                            </a>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-layouts.trainer>

