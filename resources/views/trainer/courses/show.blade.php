<x-app-layout>
    @php
        $canPublishCourse = $course->hasReadyModuleQuizzes();
        if ($course->status === 'published' || $canPublishCourse) {
            $activeCreationStep = 5;
        } elseif ($course->modules->isEmpty()) {
            $activeCreationStep = 2;
        } elseif ($course->modules->contains(fn ($m) => $m->materials->isEmpty())) {
            $activeCreationStep = 3;
        } else {
            $activeCreationStep = 4;
        }
    @endphp

    <x-slot name="header">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="min-w-0">
                <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2">
                    <a href="{{ route('trainer.courses.index') }}" class="hover:text-emerald-600 transition font-medium">
                        My Courses
                    </a>
                    <span>/</span>
                    <span class="text-slate-800 font-semibold truncate max-w-md">{{ $course->title }}</span>
                </nav>

                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight truncate">
                        {{ $course->title }}
                    </h1>

                    @if ($course->status === 'published')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                            Published
                        </span>
                    @elseif ($course->status === 'unpublished')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                            Unpublished
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                            <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                            Draft
                        </span>
                    @endif
                </div>

                <p class="text-sm text-slate-500 mt-1 max-w-3xl">
                    {{ $course->description }}
                </p>
            </div>

            <!-- Course Actions -->
            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a href="{{ route('trainer.courses.index') }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-sm">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Courses
                </a>

                <a href="{{ route('trainer.courses.edit', $course->id) }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-sm">
                    Edit Course
                </a>

                @if ($course->status === 'published')
                    <form method="POST" action="{{ route('trainer.courses.unpublish', $course->id) }}"
                          onsubmit="return confirm('Unpublish this course? Teachers will no longer be able to access it.');">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-amber-300 bg-amber-50 text-xs font-semibold text-amber-800 hover:bg-amber-100 transition shadow-sm">
                            Unpublish Course
                        </button>
                    </form>
                @elseif ($canPublishCourse)
                    <form method="POST" action="{{ route('trainer.courses.publish', $course->id) }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 text-xs font-semibold text-white hover:bg-emerald-700 shadow-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Publish Course
                        </button>
                    </form>
                @endif

                <form method="POST" action="{{ route('trainer.courses.destroy', $course->id) }}"
                      onsubmit="return confirm('Are you sure you want to delete this course?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center px-3.5 py-2 rounded-xl border border-red-200 bg-white text-xs font-semibold text-red-600 hover:bg-red-50 transition">
                        Delete Course
                    </button>
                </form>

                @if ($course->status !== 'published' && ! $canPublishCourse)
                    <p class="w-full lg:text-right text-xs font-medium text-amber-700 mt-1">
                        @if ($course->modules->isEmpty())
                            Add at least 1 module and publish its quiz to unlock publishing.
                        @else
                            Publish the quiz for <strong>{{ $course->incompleteQuizModules()->pluck('title')->join(', ') }}</strong> to unlock publishing.
                        @endif
                    </p>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50/70 min-h-[calc(100vh-10rem)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            @if (session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-900 shadow-sm flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-900 shadow-sm">
                    <p class="font-bold">Please resolve the following:</p>
                    <ul class="mt-1.5 list-disc list-inside text-xs text-red-700 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($course->status === 'published' && $course->workshops->whereIn('status', ['published', 'ongoing'])->isEmpty())
                @php
                    $draftWorkshop = $course->workshops->firstWhere('status', 'draft');
                @endphp
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50/90 p-5 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-emerald-950">Published Course — Schedule a Workshop for Teachers</h3>
                            <p class="text-xs text-emerald-800 mt-0.5">
                                Teachers browse and enroll through <strong>Workshop Management</strong>. Create and publish a workshop linked to <strong>{{ $course->title }}</strong> so it appears on <em>Discover Workshops</em>.
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        @if ($draftWorkshop)
                            <a href="{{ route('trainer.workshops.show', $draftWorkshop) }}"
                               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-emerald-600 text-xs font-semibold text-white hover:bg-emerald-700 shadow-sm transition">
                                Open Draft Workshop &amp; Publish →
                            </a>
                        @else
                            <a href="{{ route('trainer.workshops.create', ['course_id' => $course->id]) }}"
                               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-emerald-600 text-xs font-semibold text-white hover:bg-emerald-700 shadow-sm transition">
                                Create Workshop for {{ $course->title }} →
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                <div class="lg:col-span-2 space-y-8">
                    <!-- Course Metadata Summary Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Learning Objectives</p>
                        <p class="mt-1.5 text-sm text-slate-700 whitespace-pre-line leading-relaxed">{{ $course->learning_objectives }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Target Participants</p>
                        <p class="mt-1.5 text-sm text-slate-700 leading-relaxed">{{ $course->target_participants }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Estimated Duration</p>
                        <p class="mt-1 text-2xl font-extrabold text-slate-900">{{ $course->estimated_duration }} <span class="text-sm font-semibold text-slate-500">minutes</span></p>
                        <p class="mt-1 text-xs text-slate-500">
                            {{ $course->modules->count() }} {{ Str::plural('module', $course->modules->count()) }} configured
                        </p>
                    </div>
                </div>
            </div>

            <!-- Modules Section -->
            <div class="space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900 tracking-tight">Course Modules</h2>
                        <p class="text-sm text-slate-500 mt-0.5">Sequential learning units, training materials, and module quizzes.</p>
                    </div>

                    <a href="{{ route('trainer.modules.create', $course->id) }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 shadow-sm transition shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Module
                    </a>
                </div>

                @if ($course->modules->count() > 0)
                    <div class="space-y-6">
                        @foreach ($course->modules as $module)
                            @php
                                $quizReady = $course->isModuleQuizReady($module);
                            @endphp
                            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                                <!-- Module Card Header -->
                                <div class="p-6 border-b border-slate-100 bg-slate-50/40 flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                                    <div class="space-y-2">
                                        <div class="flex flex-wrap items-center gap-2.5">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                Module {{ $module->sequence }}
                                            </span>
                                            <h3 class="text-lg font-bold text-slate-900">
                                                {{ $module->title }}
                                            </h3>
                                            @if ($module->is_required)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                                    Required
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                                    Optional
                                                </span>
                                            @endif
                                            <span class="inline-flex items-center gap-1 text-xs text-slate-500">
                                                &bull; {{ $module->estimated_duration }} min
                                            </span>
                                        </div>

                                        <p class="text-sm text-slate-600 leading-relaxed">{{ $module->description }}</p>
                                    </div>

                                    <div class="flex items-center gap-2 shrink-0">
                                        <a href="{{ route('trainer.modules.edit', [$course->id, $module->id]) }}"
                                           class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                            Edit
                                        </a>
                                        <form method="POST"
                                              action="{{ route('trainer.modules.destroy', [$course->id, $module->id]) }}"
                                              onsubmit="return confirm('Delete this module?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="px-3 py-1.5 rounded-lg border border-red-200 bg-white text-xs font-semibold text-red-600 hover:bg-red-50 transition">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div class="p-6 space-y-6">
                                    <!-- Learning Objectives -->
                                    <div class="rounded-xl bg-slate-50 border border-slate-200/70 p-4">
                                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Learning Objectives</p>
                                        <p class="mt-1.5 text-sm text-slate-700 whitespace-pre-line">{{ $module->learning_objectives }}</p>
                                    </div>

                                    <!-- Module Quiz Banner -->
                                    <div class="flex flex-wrap items-center justify-between gap-4 rounded-xl border p-4 {{ $quizReady ? 'border-emerald-200 bg-emerald-50/70' : 'border-amber-200 bg-amber-50/70' }}">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 {{ $quizReady ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 class="text-sm font-bold text-slate-900">Module Quiz</h4>
                                                @if ($quizReady)
                                                    <p class="text-xs font-medium text-emerald-700">
                                                        Published &bull; {{ $module->quiz->questions->count() }} {{ Str::plural('question', $module->quiz->questions->count()) }}
                                                    </p>
                                                @else
                                                    <p class="text-xs font-medium text-amber-800">
                                                        {{ $module->quiz?->questions->isEmpty() ?? true ? 'No questions yet' : 'Draft mode' }} &bull; publish quiz before publishing course
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        <a href="{{ route('trainer.modules.quiz.edit', [$course->id, $module->id]) }}"
                                           class="inline-flex items-center gap-1.5 rounded-xl px-4 py-2 text-xs font-semibold text-white shadow-sm transition {{ $quizReady ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-amber-600 hover:bg-amber-700' }}">
                                            {{ $quizReady ? 'Manage Quiz' : 'Setup & Publish Quiz' }}
                                        </a>
                                    </div>

                                    <!-- Training Materials -->
                                    <div>
                                        <div class="flex items-center justify-between gap-4 mb-3">
                                            <div>
                                                <h4 class="text-sm font-bold text-slate-900">Training Materials</h4>
                                                <p class="text-xs text-slate-500">Guides, handouts, slides, videos, or reference links for this module.</p>
                                            </div>
                                            <a href="{{ route('trainer.materials.create', [$course->id, $module->id]) }}"
                                               class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50 px-3.5 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-100 transition">
                                                + Add Material
                                            </a>
                                        </div>

                                        @if ($module->materials->count() > 0)
                                            <div class="divide-y divide-slate-100 rounded-xl border border-slate-200 bg-white">
                                                @foreach ($module->materials as $material)
                                                    <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/70 transition">
                                                        <div class="min-w-0">
                                                            <div class="flex items-center gap-2">
                                                                <p class="text-sm font-semibold text-slate-900 truncate">{{ $material->title }}</p>
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-600">
                                                                    {{ ucfirst(str_replace('_', ' ', $material->type)) }}
                                                                </span>
                                                            </div>
                                                            @if ($material->description)
                                                                <p class="text-xs text-slate-500 mt-1">{{ $material->description }}</p>
                                                            @endif
                                                        </div>

                                                        <div class="flex items-center gap-3 shrink-0">
                                                            @if ($material->external_url)
                                                                <a href="{{ $material->external_url }}" target="_blank"
                                                                   class="text-xs font-semibold text-emerald-600 hover:text-emerald-800">
                                                                    Open Link
                                                                </a>
                                                            @elseif ($material->file_path)
                                                                <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank"
                                                                   class="text-xs font-semibold text-emerald-600 hover:text-emerald-800">
                                                                    Open File
                                                                </a>
                                                            @endif

                                                            <a href="{{ route('trainer.materials.edit', [$course->id, $module->id, $material->id]) }}"
                                                               class="text-xs font-semibold text-slate-600 hover:text-slate-900">
                                                                Edit
                                                            </a>

                                                            <form method="POST"
                                                                  action="{{ route('trainer.materials.destroy', [$course->id, $module->id, $material->id]) }}"
                                                                  onsubmit="return confirm('Delete this training material?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-800">
                                                                    Delete
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50/50 p-5 text-center">
                                                <p class="text-xs text-slate-500">No training materials have been added yet.</p>
                                                <a href="{{ route('trainer.materials.create', [$course->id, $module->id]) }}"
                                                   class="inline-block mt-2 text-xs font-semibold text-emerald-700 hover:text-emerald-800">
                                                    + Add the first material
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-12 text-center shadow-sm">
                        <div class="mx-auto w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">No modules yet</h3>
                        <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">
                            Start building this course by adding your first module, uploading materials, and publishing its module quiz.
                        </p>
                        <a href="{{ route('trainer.modules.create', $course->id) }}"
                           class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-semibold hover:bg-emerald-700 shadow-sm transition">
                            + Add Your First Module
                        </a>
                    </div>
                @endif
            </div>
                </div>

                <!-- Right Sidebar: Course Creation Steps -->
                <div class="space-y-6 lg:sticky lg:top-6">
                    @include('trainer.courses._creation-steps', ['activeStep' => $activeCreationStep, 'course' => $course])
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
