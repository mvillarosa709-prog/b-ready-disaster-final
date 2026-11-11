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
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-400 transition shadow-sm"
                >
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Workshops
                </a>

                @if ($workshop->status !== 'published' && $canPublish)
                    <form method="POST" action="{{ route('trainer.workshops.publish', $workshop) }}">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 shadow-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Publish Workshop
                        </button>
                    </form>
                @endif

                <a
                    href="{{ route('trainer.workshops.edit', $workshop) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-300 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 shadow-sm transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Settings
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        @if ($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-900 shadow-sm">
                <p class="font-bold">Please fix the following:</p>
                <ul class="mt-1.5 list-disc list-inside text-xs text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
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
                                        @if ($module->quiz && $module->quiz->is_published && $module->quiz->questions->isNotEmpty())
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

                <!-- Classroom Package (Teacher Workshop Package) -->
                @php
                    $pkg = $workshop->classroomPackage;
                @endphp
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-base font-bold text-slate-900">Classroom Package (Teacher Workshop Package)</h2>
                                @if ($pkg)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">
                                        Configured
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600">
                                        Not set up yet
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Teaching toolkit and downloadable guides that certified teachers unlock in <strong>Workshop Packages</strong> after completing this workshop.
                            </p>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <form method="POST" action="{{ route('trainer.workshops.package.save', $workshop) }}" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="package_title" class="block text-xs font-semibold text-slate-700 mb-1">Package Title <span class="text-red-500">*</span></label>
                                    <input id="package_title" type="text" name="title" required maxlength="255"
                                        value="{{ old('title', $pkg?->title ?? $workshop->title . ' — Classroom Package') }}"
                                        class="block w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500/20">
                                </div>
                                <div class="flex items-end justify-between gap-3">
                                    <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700 pb-2.5 cursor-pointer">
                                        <input type="checkbox" name="is_published" value="1" {{ old('is_published', $pkg?->is_published ?? true) ? 'checked' : '' }}
                                            class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                        Available to certified teachers
                                    </label>
                                    <button type="submit"
                                        class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800 transition shrink-0">
                                        Save Package Details
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label for="package_description" class="block text-xs font-semibold text-slate-700 mb-1">Package Description</label>
                                <textarea id="package_description" name="description" rows="2"
                                    placeholder="Describe the student manuals, activity worksheets, or rubrics included in this package."
                                    class="block w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500/20">{{ old('description', $pkg?->description) }}</textarea>
                            </div>
                        </form>

                        <div class="pt-5 border-t border-slate-100 space-y-4">
                            <h3 class="text-sm font-bold text-slate-900">Package Materials ({{ $pkg?->materials->count() ?? 0 }})</h3>

                            @if ($pkg && $pkg->materials->isNotEmpty())
                                <div class="divide-y divide-slate-100 rounded-xl border border-slate-200 bg-slate-50/40">
                                    @foreach ($pkg->materials as $material)
                                        <div class="p-3.5 flex items-center justify-between gap-3">
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <p class="text-sm font-semibold text-slate-900 truncate">{{ $material->title }}</p>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-white border border-slate-200 text-slate-600">
                                                        {{ ucfirst(str_replace('_', ' ', $material->material_type)) }}
                                                    </span>
                                                </div>
                                                @if ($material->original_filename)
                                                    <p class="text-[11px] text-slate-500 mt-0.5 truncate">{{ $material->original_filename }}</p>
                                                @endif
                                            </div>
                                            <form method="POST" action="{{ route('trainer.workshops.package.materials.destroy', [$workshop, $material]) }}"
                                                  onsubmit="return confirm('Remove this classroom material?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-800 shrink-0">Remove</button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <form method="POST" action="{{ route('trainer.workshops.package.materials.store', $workshop) }}" enctype="multipart/form-data"
                                  class="rounded-xl border border-dashed border-slate-300 bg-slate-50/60 p-4 space-y-3">
                                @csrf
                                <p class="text-xs font-bold text-slate-700">+ Add Classroom Material to Package</p>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <input type="text" name="title" required maxlength="255" placeholder="Material title (e.g. Drill Worksheet)"
                                            class="block w-full rounded-xl border-slate-300 bg-white text-xs focus:border-emerald-500 focus:ring-emerald-500/20">
                                    </div>
                                    <div>
                                        <select name="material_type" required class="block w-full rounded-xl border-slate-300 bg-white text-xs focus:border-emerald-500 focus:ring-emerald-500/20">
                                            <option value="teacher_guide">Teacher Guide</option>
                                            <option value="student_manual">Student Manual</option>
                                            <option value="classroom_activity">Classroom Activity</option>
                                            <option value="worksheet">Worksheet</option>
                                            <option value="student_assessment">Student Assessment</option>
                                            <option value="answer_key">Answer Key</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    <div>
                                        <input type="file" name="file" required
                                            class="block w-full text-xs text-slate-600 file:mr-2 file:rounded-lg file:border file:border-slate-300 file:bg-white file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-emerald-700">
                                    </div>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <input type="text" name="description" maxlength="2000" placeholder="Short description (optional)"
                                        class="flex-1 rounded-xl border-slate-300 bg-white text-xs focus:border-emerald-500 focus:ring-emerald-500/20">
                                    <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-xs font-semibold text-white hover:bg-emerald-700 transition shrink-0">
                                        Upload Material
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
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
                            <p class="mt-1">The attached course and all module quizzes are published. Click <strong>Publish Workshop Now</strong> below to go live.</p>
                        </div>
                    @else
                        <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900">
                            <p class="font-bold">Draft — Course Incomplete</p>
                            <p class="mt-1">Publish the attached course and ensure every module has a published quiz before publishing this workshop.</p>
                        </div>
                    @endif

                    <div class="pt-2 border-t border-slate-100 flex flex-col gap-2">
                        @if ($workshop->status !== 'published' && $canPublish)
                            <form method="POST" action="{{ route('trainer.workshops.publish', $workshop) }}">
                                @csrf
                                <button type="submit" class="w-full text-center px-4 py-2.5 rounded-xl bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700 shadow-sm transition">
                                    Publish Workshop Now →
                                </button>
                            </form>
                        @endif
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

