<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2">
                    <a href="{{ route('trainer.courses.show', $course->id) }}" class="hover:text-emerald-600 transition font-medium truncate max-w-xs">
                        {{ $course->title }}
                    </a>
                    <span>/</span>
                    <span class="text-slate-600">Module {{ $module->sequence }}</span>
                    <span>/</span>
                    <span class="text-slate-800 font-semibold">Edit Module</span>
                </nav>

                <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-bold text-slate-900 tracking-tight">
                        {{ __('Edit Module') }}
                    </h2>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        Module {{ $module->sequence }}
                    </span>
                </div>
                <p class="text-sm text-slate-500 mt-1">
                    Update module details for <strong>{{ $course->title }}</strong>.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a
                    href="{{ route('trainer.courses.show', $course->id) }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-400 transition shadow-sm"
                >
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Course
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50/70 min-h-[calc(100vh-10rem)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

                <!-- Main Form (2 cols) -->
                <div class="lg:col-span-2 space-y-6">

                    @if ($errors->any())
                        <div class="rounded-2xl bg-red-50 border border-red-200 p-4 shadow-sm">
                            <div class="flex items-start gap-3">
                                <div class="shrink-0 p-1 bg-red-100 rounded-lg text-red-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-sm font-bold text-red-900">Please fix the following errors:</h3>
                                    <ul class="mt-2 list-disc list-inside text-xs text-red-700 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('trainer.modules.update', [$course->id, $module->id]) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Card 1: Module Essentials -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-7 space-y-6">
                            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-900">Module Essentials</h3>
                                    <p class="text-xs text-slate-500">Define the module title and what teachers will cover.</p>
                                </div>
                            </div>

                            <div>
                                <label for="title" class="block text-sm font-semibold text-slate-800 mb-1.5">
                                    Module Title <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="title"
                                    name="title"
                                    value="{{ old('title', $module->title) }}"
                                    required
                                    class="block w-full rounded-xl border-slate-300 shadow-sm text-slate-900 text-sm focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3.5 transition"
                                >
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-semibold text-slate-800 mb-1.5">
                                    Module Description <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    id="description"
                                    name="description"
                                    rows="4"
                                    required
                                    class="block w-full rounded-xl border-slate-300 shadow-sm text-slate-900 text-sm focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3.5 transition"
                                >{{ old('description', $module->description) }}</textarea>
                            </div>
                        </div>

                        <!-- Card 2: Learning Objectives -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-7 space-y-6">
                            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-900">Learning Objectives</h3>
                                    <p class="text-xs text-slate-500">Specify the skills teachers should master after completing this unit.</p>
                                </div>
                            </div>

                            <div>
                                <label for="learning_objectives" class="block text-sm font-semibold text-slate-800 mb-1.5">
                                    Key Outcomes <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    id="learning_objectives"
                                    name="learning_objectives"
                                    rows="4"
                                    required
                                    class="block w-full rounded-xl border-slate-300 shadow-sm text-slate-900 text-sm focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3.5 transition"
                                >{{ old('learning_objectives', $module->learning_objectives) }}</textarea>
                            </div>
                        </div>

                        <!-- Card 3: Duration, Order & Requirement -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-7 space-y-6">
                            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-900">Duration &amp; Sequencing</h3>
                                    <p class="text-xs text-slate-500">Set the module duration and its sequential position in the course.</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label for="estimated_duration" class="block text-sm font-semibold text-slate-800 mb-1.5">
                                        Estimated Duration <span class="text-red-500">*</span>
                                    </label>
                                    <div class="flex rounded-xl shadow-sm">
                                        <input
                                            type="number"
                                            id="estimated_duration"
                                            name="estimated_duration"
                                            value="{{ old('estimated_duration', $module->estimated_duration) }}"
                                            min="1"
                                            required
                                            class="block w-full rounded-l-xl border-slate-300 text-slate-900 text-sm focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3.5 transition"
                                        >
                                        <span class="inline-flex items-center px-3.5 rounded-r-xl border border-l-0 border-slate-300 bg-slate-50 text-slate-600 text-xs font-semibold">
                                            Minutes
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <label for="sequence" class="block text-sm font-semibold text-slate-800 mb-1.5">
                                        Module Order <span class="text-red-500">*</span>
                                    </label>
                                    <div class="flex rounded-xl shadow-sm">
                                        <span class="inline-flex items-center px-3.5 rounded-l-xl border border-r-0 border-slate-300 bg-slate-50 text-slate-600 text-xs font-semibold">
                                            #
                                        </span>
                                        <input
                                            type="number"
                                            id="sequence"
                                            name="sequence"
                                            value="{{ old('sequence', $module->sequence) }}"
                                            min="1"
                                            required
                                            class="block w-full rounded-r-xl border-slate-300 text-slate-900 text-sm focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3.5 transition"
                                        >
                                    </div>
                                    <p class="mt-1 text-xs text-slate-500">Order in which teachers unlock modules.</p>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-slate-100">
                                <label class="inline-flex items-center gap-2.5 cursor-pointer">
                                    <input
                                        type="checkbox"
                                        name="is_required"
                                        value="1"
                                        {{ old('is_required', $module->is_required) ? 'checked' : '' }}
                                        class="rounded border-slate-300 text-emerald-600 shadow-sm focus:ring-emerald-500"
                                    >
                                    <span class="text-sm font-medium text-slate-700">
                                        This module is required for certification
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Bottom Actions -->
                        <div class="flex items-center justify-between gap-4 pt-2">
                            <a
                                href="{{ route('trainer.courses.show', $course->id) }}"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition shadow-sm"
                            >
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Cancel &amp; Return
                            </a>

                            <button
                                type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 shadow-sm transition transform active:scale-95"
                            >
                                <span>Save Module Changes</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                        </div>
                    </form>

                </div>

                <!-- Right Sidebar -->
                <div class="space-y-6">
                    @include('trainer.courses._creation-steps', ['activeStep' => 2, 'course' => $course])

                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-700 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Quick Actions
                        </h4>
                        <div class="flex flex-col gap-2 pt-1">
                            <a href="{{ route('trainer.materials.create', [$course->id, $module->id]) }}"
                               class="w-full text-center px-3.5 py-2 rounded-xl border border-emerald-200 bg-emerald-50 text-xs font-semibold text-emerald-700 hover:bg-emerald-100 transition">
                                + Add Material to Module
                            </a>
                            <a href="{{ route('trainer.modules.quiz.edit', [$course->id, $module->id]) }}"
                               class="w-full text-center px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                Manage Module Quiz
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

