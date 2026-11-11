<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <!-- Clean Breadcrumbs -->
                <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2">
                    <a href="{{ route('trainer.courses.index') }}" class="hover:text-emerald-600 transition font-medium">
                        My Courses
                    </a>
                    <span>/</span>
                    <span class="text-slate-800 font-semibold">New Course</span>
                </nav>

                <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-bold text-slate-900 tracking-tight">
                        {{ __('Create New Course') }}
                    </h2>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                        Draft Mode
                    </span>
                </div>
                <p class="text-sm text-slate-500 mt-1">
                    Define the core curriculum details. Next, you'll add modules, materials, and quizzes.
                </p>
            </div>

            <!-- Single Back Action on the right side -->
            <div class="flex items-center gap-2">
                <a
                    href="{{ route('trainer.courses.index') }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-400 transition shadow-sm"
                >
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Courses
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

                    <form method="POST" action="{{ route('trainer.courses.store') }}" id="createCourseForm">
                        @csrf

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
                                    <p class="text-xs text-slate-500">Provide the title and summary description for this training curriculum.</p>
                                </div>
                            </div>

                            {{-- Course Title --}}
                            <div>
                                <label for="title" class="block text-sm font-semibold text-slate-800 mb-1.5">
                                    Course Title <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input
                                        type="text"
                                        id="title"
                                        name="title"
                                        value="{{ old('title') }}"
                                        placeholder="e.g. Earthquake Safety & School Evacuation Protocols"
                                        required
                                        class="block w-full rounded-xl border-slate-300 shadow-xs text-slate-900 text-sm focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3.5 placeholder:text-slate-400 transition"
                                    >
                                </div>
                                <p class="mt-1.5 text-xs text-slate-500">
                                    Choose a clear, specific name that highlights the disaster preparedness subject.
                                </p>
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
                                    placeholder="Write a concise overview of what teachers will learn, the context of the disaster scenarios, and why this training is important."
                                    required
                                    class="block w-full rounded-xl border-slate-300 shadow-xs text-slate-900 text-sm focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3.5 placeholder:text-slate-400 transition"
                                >{{ old('description') }}</textarea>
                                <p class="mt-1.5 text-xs text-slate-500">
                                    Summarize the course scope and key takeaways.
                                </p>
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
                                    <p class="text-xs text-slate-500">Clarify the expected learning outcomes and who should enroll.</p>
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
                                    placeholder="• Understand DepEd disaster protocols during earthquakes
• Identify immediate classroom evacuation routes and hazard zones
• Conduct basic student triage and post-incident headcount"
                                    required
                                    class="block w-full rounded-xl border-slate-300 shadow-xs text-slate-900 text-sm focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3.5 placeholder:text-slate-400 transition font-sans"
                                >{{ old('learning_objectives') }}</textarea>
                                <p class="mt-1.5 text-xs text-slate-500">
                                    List specific, measurable skills and competencies teachers will acquire.
                                </p>
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
                                    placeholder="e.g. Public and private school K-12 teachers, School DRRM coordinators, School heads, and education staff."
                                    required
                                    class="block w-full rounded-xl border-slate-300 shadow-xs text-slate-900 text-sm focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3.5 placeholder:text-slate-400 transition"
                                >{{ old('target_participants') }}</textarea>
                                <p class="mt-1.5 text-xs text-slate-500">
                                    Specify which grade levels, positions, or roles will benefit most from this course.
                                </p>
                            </div>
                        </div>

                        <!-- Card 3: Duration & Scheduling -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-7 space-y-6 mb-6">
                            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg shadow-xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-900">Estimated Duration</h3>
                                    <p class="text-xs text-slate-500">Estimate how long it takes an average teacher to complete the course.</p>
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
                                        value="{{ old('estimated_duration', 60) }}"
                                        min="1"
                                        placeholder="60"
                                        required
                                        class="block w-full rounded-l-xl border-slate-300 text-slate-900 text-sm focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3.5 transition"
                                    >
                                    <span class="inline-flex items-center px-4 rounded-r-xl border border-l-0 border-slate-300 bg-slate-50 text-slate-600 text-sm font-semibold">
                                        Minutes
                                    </span>
                                </div>
                                <p class="mt-1.5 text-xs text-slate-500">
                                    Recommended: 45 to 120 minutes per course.
                                </p>
                            </div>
                        </div>

                        <!-- Bottom Form Actions -->
                        <div class="flex items-center justify-between gap-4 pt-2">
                            <a
                                href="{{ route('trainer.courses.index') }}"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-400 transition shadow-xs"
                            >
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Cancel & Back
                            </a>

                            <button
                                type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 shadow-sm shadow-emerald-600/30 transition transform active:scale-95"
                            >
                                <span>Create Course & Continue</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>
                        </div>
                    </form>

                </div>

                <!-- Right Sidebar (Guide & Tips) (1 col) -->
                <div class="space-y-6">
                    @include('trainer.courses._creation-steps', ['activeStep' => 1])

                    <!-- Best Practices Card -->
                    <div class="rounded-2xl border border-emerald-100 bg-gradient-to-br from-emerald-50/80 to-teal-50/40 p-5">
                        <div class="flex items-start gap-3">
                            <span class="text-2xl">💡</span>
                            <div>
                                <h4 class="text-sm font-bold text-emerald-950">Trainer Tips</h4>
                                <ul class="mt-2 text-xs text-emerald-900 space-y-2">
                                    <li class="flex items-start gap-1.5">
                                        <span class="text-emerald-600 font-bold">•</span>
                                        <span>Draft courses are private until you explicitly hit <strong>Publish</strong>.</span>
                                    </li>
                                    <li class="flex items-start gap-1.5">
                                        <span class="text-emerald-600 font-bold">•</span>
                                        <span>Break large disaster topics into bite-sized 15-minute modules.</span>
                                    </li>
                                    <li class="flex items-start gap-1.5">
                                        <span class="text-emerald-600 font-bold">•</span>
                                        <span>Real-world drills and simulation exercises help reinforce classroom safety.</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>
