<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <!-- Clean Breadcrumbs -->
                <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2">
                    <a href="{{ route('trainer.courses.show', $course->id) }}" class="hover:text-emerald-600 transition font-medium">
                        {{ $course->title }}
                    </a>
                    <span>/</span>
                    <span class="text-slate-800 font-semibold">Edit Course</span>
                </nav>

                <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-bold text-slate-900 tracking-tight">
                        {{ __('Edit Course Details') }}
                    </h2>
                    @if ($course->status === 'published')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            Published
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                            Draft Mode
                        </span>
                    @endif
                </div>
                <p class="text-sm text-slate-500 mt-1">
                    Update information for <strong>{{ $course->title }}</strong>.
                </p>
            </div>

            <!-- Top Back Action -->
            <div class="flex items-center gap-2">
                <a
                    href="{{ route('trainer.courses.show', $course->id) }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-400 transition shadow-sm"
                >
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Course Details
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50/70 min-h-[calc(100vh-10rem)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                
                <!-- Main Form (2 cols) -->
                <div class="lg:col-span-2 space-y-6">

                    {{-- Validation Errors --}}
                    @if ($errors->any())
                        <div class="rounded-2xl bg-red-50 border border-red-200 p-4 shadow-xs">
                            <div class="flex items-start gap-3">
                                <div class="shrink-0 p-1 bg-red-100 rounded-lg text-red-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-sm font-bold text-red-900">
                                        Please resolve the following input issues:
                                    </h3>
                                    <ul class="mt-2 list-disc list-inside text-xs text-red-700 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('trainer.courses.update', $course->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- Card 1: Course Essentials -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-7 space-y-6 mb-6">
                            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg shadow-xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-900">Course Essentials</h3>
                                    <p class="text-xs text-slate-500">Edit course title and summary description.</p>
                                </div>
                            </div>

                            {{-- Course Title --}}
                            <div>
                                <label for="title" class="block text-sm font-semibold text-slate-800 mb-1.5">
                                    Course Title <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="title"
                                    name="title"
                                    value="{{ old('title', $course->title) }}"
                                    required
                                    class="block w-full rounded-xl border-slate-300 shadow-xs text-slate-900 text-sm focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3.5 transition"
                                >
                            </div>

                            {{-- Description --}}
                            <div>
                                <label for="description" class="block text-sm font-semibold text-slate-800 mb-1.5">
                                    Course Description <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    id="description"
                                    name="description"
                                    rows="4"
                                    required
                                    class="block w-full rounded-xl border-slate-300 shadow-xs text-slate-900 text-sm focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3.5 transition"
                                >{{ old('description', $course->description) }}</textarea>
                            </div>
                        </div>

                        <!-- Card 2: Learning Objectives & Audience -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-7 space-y-6 mb-6">
                            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg shadow-xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-900">Objectives & Target Audience</h3>
                                    <p class="text-xs text-slate-500">Refine the competencies and audience definition.</p>
                                </div>
                            </div>

                            {{-- Learning Objectives --}}
                            <div>
                                <label for="learning_objectives" class="block text-sm font-semibold text-slate-800 mb-1.5">
                                    Learning Objectives <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    id="learning_objectives"
                                    name="learning_objectives"
                                    rows="4"
                                    required
                                    class="block w-full rounded-xl border-slate-300 shadow-xs text-slate-900 text-sm focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3.5 transition"
                                >{{ old('learning_objectives', $course->learning_objectives) }}</textarea>
                            </div>

                            {{-- Target Participants --}}
                            <div>
                                <label for="target_participants" class="block text-sm font-semibold text-slate-800 mb-1.5">
                                    Target Participants <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    id="target_participants"
                                    name="target_participants"
                                    rows="3"
                                    required
                                    class="block w-full rounded-xl border-slate-300 shadow-xs text-slate-900 text-sm focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3.5 transition"
                                >{{ old('target_participants', $course->target_participants) }}</textarea>
                            </div>
                        </div>

                        <!-- Card 3: Duration -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-7 space-y-6 mb-6">
                            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg shadow-xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-900">Estimated Duration</h3>
                                    <p class="text-xs text-slate-500">Total duration in minutes.</p>
                                </div>
                            </div>

                            <div>
                                <label for="estimated_duration" class="block text-sm font-semibold text-slate-800 mb-1.5">
                                    Course Duration <span class="text-red-500">*</span>
                                </label>
                                <div class="relative max-w-xs flex rounded-xl shadow-xs">
                                    <input
                                        type="number"
                                        id="estimated_duration"
                                        name="estimated_duration"
                                        value="{{ old('estimated_duration', $course->estimated_duration) }}"
                                        min="1"
                                        required
                                        class="block w-full rounded-l-xl border-slate-300 text-slate-900 text-sm focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3.5 transition"
                                    >
                                    <span class="inline-flex items-center px-4 rounded-r-xl border border-l-0 border-slate-300 bg-slate-50 text-slate-600 text-sm font-semibold">
                                        Minutes
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Form Actions -->
                        <div class="flex items-center justify-between gap-4 pt-2">
                            <a
                                href="{{ route('trainer.courses.show', $course->id) }}"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-400 transition shadow-xs"
                            >
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Cancel & Return
                            </a>

                            <button
                                type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 shadow-sm shadow-emerald-600/30 transition transform active:scale-95"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Save Changes</span>
                            </button>
                        </div>
                    </form>

                </div>

                <!-- Right Sidebar (Overview) (1 col) -->
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
                        <h4 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Course Structure
                        </h4>
                        <div class="space-y-3 text-xs text-slate-600">
                            <div class="flex items-center justify-between py-2 border-b border-slate-100">
                                <span class="text-slate-500">Total Modules:</span>
                                <span class="font-bold text-slate-800">{{ $course->modules->count() }}</span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-slate-100">
                                <span class="text-slate-500">Publication Status:</span>
                                <span class="font-bold capitalize text-slate-800">{{ $course->status }}</span>
                            </div>
                            <div class="flex items-center justify-between py-2">
                                <span class="text-slate-500">Last Updated:</span>
                                <span class="font-medium text-slate-800">{{ $course->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        <div class="mt-5 pt-4 border-t border-slate-100">
                            <a href="{{ route('trainer.courses.show', $course->id) }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 flex items-center justify-between">
                                <span>Manage modules & materials</span>
                                <span>→</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
