@csrf
@if ($workshop)
    @method('PUT')
@else
    <input type="hidden" name="status" value="draft">
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
    
    <!-- Main Form Fields (2 cols) -->
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

        <!-- Card 1: Workshop Essentials (with Green Badge Logo) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-7 space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Workshop Essentials</h3>
                    <p class="text-xs text-slate-500">Provide the title and overview for this training session.</p>
                </div>
            </div>

            <div>
                <label for="title" class="block text-sm font-semibold text-slate-800 mb-1.5">
                    Workshop Title <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    value="{{ old('title', $workshop?->title) }}"
                    required
                    maxlength="255"
                    placeholder="e.g. School Earthquake Response Drill & Scenario Simulation 2026"
                    class="block w-full rounded-xl border-slate-300 shadow-xs text-slate-900 text-sm focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3.5 placeholder:text-slate-400 transition"
                >
                @error('title')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                <p class="mt-1.5 text-xs text-slate-500">
                    A clear, identifiable name for teachers registering for the workshop.
                </p>
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-slate-800 mb-1.5">
                    Description <span class="font-normal text-slate-400 text-xs">(optional)</span>
                </label>
                <textarea
                    id="description"
                    name="description"
                    rows="3"
                    maxlength="10000"
                    placeholder="Provide details about the workshop venue, logistics, expectations, or pre-requisite preparation."
                    class="block w-full rounded-xl border-slate-300 shadow-xs text-slate-900 text-sm focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3.5 placeholder:text-slate-400 transition"
                >{{ old('description', $workshop?->description) }}</textarea>
                @error('description')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <!-- Card 2: Curriculum Link (with Blue Badge Logo) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-7 space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Curriculum &amp; Course Assignment</h3>
                    <p class="text-xs text-slate-500">Attach the disaster preparedness course modules and quizzes for this workshop.</p>
                </div>
            </div>

            <div>
                <label for="course_id" class="block text-sm font-semibold text-slate-800 mb-1.5">
                    Associated Course <span class="text-red-500">*</span>
                </label>
                <select
                    id="course_id"
                    name="course_id"
                    required
                    class="block w-full rounded-xl border-slate-300 shadow-xs text-slate-900 text-sm focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3.5 transition"
                >
                    <option value="">Select a course</option>
                    @foreach ($courses as $course)
                        <option
                            value="{{ $course->id }}"
                            data-publishable="{{ $course->workshop_publishable ? 'true' : 'false' }}"
                            data-modules="{{ $course->modules_count ?? $course->modules->count() }}"
                            data-duration="{{ $course->estimated_duration ?? 0 }}"
                            data-status="{{ ucfirst($course->status) }}"
                            @selected((string) old('course_id', $workshop?->course_id ?? request('course_id')) === (string) $course->id)
                        >
                            {{ $course->title }} — {{ ucfirst($course->status) }}
                        </option>
                    @endforeach
                </select>
                @error('course_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror

                <!-- Dynamic Course Preview Box -->
                <div id="coursePreviewBox" class="hidden mt-3 p-4 rounded-xl bg-slate-50 border border-slate-200/80 text-xs text-slate-600 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-800">Selected Curriculum Summary</span>
                        <span id="previewCourseStatus" class="px-2 py-0.5 rounded-full text-[11px] font-semibold"></span>
                    </div>
                    <div class="flex items-center gap-4 text-slate-600">
                        <span>Modules included: <strong id="previewModulesCount" class="text-slate-900">0</strong></span>
                        <span>•</span>
                        <span>Estimated duration: <strong id="previewDuration" class="text-slate-900">0</strong> mins</span>
                    </div>
                    <p id="previewReadinessNote" class="text-[11px] text-slate-500 pt-1"></p>
                </div>

                @if ($courses->isEmpty())
                    <div class="mt-2 p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800 flex items-center gap-2">
                        <span>⚠️</span>
                        <span>Create a course first in Course Management, then return here to schedule its workshop.</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Card 3: Schedule & Deadlines (with Amber Badge Logo) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-7 space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Schedule &amp; Registration Deadlines</h3>
                    <p class="text-xs text-slate-500">Specify dates and times for registration and live sessions.</p>
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-3">
                <div>
                    <label for="start_date" class="block text-sm font-semibold text-slate-800 mb-1.5">
                        Starts <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="start_date"
                        type="datetime-local"
                        name="start_date"
                        value="{{ old('start_date', $workshop?->start_date?->format('Y-m-d\TH:i')) }}"
                        required
                        class="block w-full rounded-xl border-slate-300 shadow-xs text-slate-900 text-sm focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3 transition"
                    >
                    @error('start_date')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-semibold text-slate-800 mb-1.5">
                        Ends <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="end_date"
                        type="datetime-local"
                        name="end_date"
                        value="{{ old('end_date', $workshop?->end_date?->format('Y-m-d\TH:i')) }}"
                        required
                        class="block w-full rounded-xl border-slate-300 shadow-xs text-slate-900 text-sm focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3 transition"
                    >
                    @error('end_date')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="registration_deadline" class="block text-sm font-semibold text-slate-800 mb-1.5">
                        Registration Deadline <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="registration_deadline"
                        type="datetime-local"
                        name="registration_deadline"
                        value="{{ old('registration_deadline', $workshop?->registration_deadline?->format('Y-m-d\TH:i')) }}"
                        required
                        class="block w-full rounded-xl border-slate-300 shadow-xs text-slate-900 text-sm focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3 transition"
                    >
                    @error('registration_deadline')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>
            <p class="text-xs text-slate-500">
                Teachers will not be able to join or enroll after the registration deadline.
            </p>
        </div>

        @if ($workshop)
            <!-- Card 4: Publication Status (ONLY shown when Editing an existing workshop) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-7 space-y-6">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-lg shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Workshop Status</h3>
                        <p class="text-xs text-slate-500">Control when teachers can see and register for this workshop.</p>
                    </div>
                </div>

                <div>
                    <label for="status" class="block text-sm font-semibold text-slate-800 mb-1.5">
                        Publication Status <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="status"
                        name="status"
                        required
                        class="block max-w-xs w-full rounded-xl border-slate-300 shadow-xs text-slate-900 text-sm focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3.5 transition"
                    >
                        @if ($workshop->status !== 'published' && $workshop->status !== 'ongoing')
                            <option value="draft" @selected(old('status', $workshop?->status ?? 'draft') === 'draft')>Draft (Hidden from Teachers)</option>
                        @endif
                        <option
                            value="published"
                            @disabled(! $courses->firstWhere('id', (int) old('course_id', $workshop?->course_id))?->workshop_publishable)
                            @selected(old('status', $workshop?->status) === 'published' || $workshop?->status === 'ongoing')
                        >
                            Published (Open for Registration)
                        </option>
                    </select>
                    @error('status')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>
        @endif

        <!-- Bottom Form Actions -->
        <div class="flex items-center justify-between gap-4 pt-2">
            <a
                href="{{ $workshop ? route('trainer.workshops.show', $workshop) : route('trainer.workshops.index') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-400 transition shadow-xs"
            >
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Cancel &amp; Return
            </a>

            <button
                type="submit"
                @disabled($courses->isEmpty())
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 shadow-sm shadow-emerald-600/30 transition transform active:scale-95 disabled:cursor-not-allowed disabled:opacity-50"
            >
                <span>{{ $workshop ? 'Save Workshop Changes' : 'Create workshop (Draft)' }}</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </div>

    </div>

    <!-- Right Sidebar (Guide & Tips) (1 col) -->
    <div class="space-y-6">
        
        <!-- Roadmap Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
            <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-700 mb-4 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                Workshop Lifecycle
            </h4>

            <ol class="relative border-l border-emerald-200 ml-3 space-y-6 text-sm">
                <li class="ml-6">
                    <span class="absolute -left-3 flex items-center justify-center w-6 h-6 rounded-full bg-emerald-600 text-white text-xs font-bold ring-4 ring-white">
                        1
                    </span>
                    <h5 class="font-bold text-slate-900">Save Schedule as Draft</h5>
                    <p class="text-xs text-emerald-700 mt-0.5 font-medium">Currently configuring</p>
                </li>
                <li class="ml-6">
                    <span class="absolute -left-3 flex items-center justify-center w-6 h-6 rounded-full bg-slate-100 text-slate-500 text-xs font-bold ring-4 ring-white">
                        2
                    </span>
                    <h5 class="font-semibold text-slate-700">Review &amp; Publish</h5>
                    <p class="text-xs text-slate-400 mt-0.5">Verify attached course &amp; publish</p>
                </li>
                <li class="ml-6">
                    <span class="absolute -left-3 flex items-center justify-center w-6 h-6 rounded-full bg-slate-100 text-slate-500 text-xs font-bold ring-4 ring-white">
                        3
                    </span>
                    <h5 class="font-semibold text-slate-700">Teacher Registration</h5>
                    <p class="text-xs text-slate-400 mt-0.5">Participants enroll before deadline</p>
                </li>
                <li class="ml-6">
                    <span class="absolute -left-3 flex items-center justify-center w-6 h-6 rounded-full bg-slate-100 text-slate-500 text-xs font-bold ring-4 ring-white">
                        4
                    </span>
                    <h5 class="font-semibold text-slate-700">Live Training &amp; Quizzes</h5>
                    <p class="text-xs text-slate-400 mt-0.5">75% passing mark on module quizzes</p>
                </li>
                <li class="ml-6">
                    <span class="absolute -left-3 flex items-center justify-center w-6 h-6 rounded-full bg-slate-100 text-slate-500 text-xs font-bold ring-4 ring-white">
                        5
                    </span>
                    <h5 class="font-semibold text-slate-700">Certificate Issuance</h5>
                    <p class="text-xs text-slate-400 mt-0.5">Approve &amp; sign teacher certificates</p>
                </li>
            </ol>
        </div>

        <!-- Best Practices Card -->
        <div class="rounded-2xl border border-emerald-100 bg-gradient-to-br from-emerald-50/80 to-teal-50/40 p-5">
            <div class="flex items-start gap-3">
                <span class="text-2xl">💡</span>
                <div>
                    <h4 class="text-sm font-bold text-emerald-950">Workshop Tips</h4>
                    <ul class="mt-2 text-xs text-emerald-900 space-y-2">
                        <li class="flex items-start gap-1.5">
                            <span class="text-emerald-600 font-bold">•</span>
                            <span>New workshops automatically start in <strong>Draft Mode</strong>. You can review participants and publish later when clicking the workshop.</span>
                        </li>
                        <li class="flex items-start gap-1.5">
                            <span class="text-emerald-600 font-bold">•</span>
                            <span>Set the registration deadline at least <strong>24 to 48 hours</strong> prior to start time.</span>
                        </li>
                        <li class="flex items-start gap-1.5">
                            <span class="text-emerald-600 font-bold">•</span>
                            <span>The attached course brings all its learning modules, slides, and quizzes directly into this workshop.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
    const workshopCourse = document.getElementById('course_id');
    const workshopStatus = document.getElementById('status');
    const publishedStatus = workshopStatus ? workshopStatus.querySelector('option[value="published"]') : null;

    const previewBox = document.getElementById('coursePreviewBox');
    const previewStatus = document.getElementById('previewCourseStatus');
    const previewModules = document.getElementById('previewModulesCount');
    const previewDuration = document.getElementById('previewDuration');
    const previewNote = document.getElementById('previewReadinessNote');

    function updateWorkshopCourseState() {
        const selectedCourse = workshopCourse.selectedOptions[0];

        if (publishedStatus && workshopStatus) {
            publishedStatus.disabled = selectedCourse?.dataset.publishable !== 'true';
            if (publishedStatus.disabled && workshopStatus.value === 'published') {
                workshopStatus.value = 'draft';
            }
        }

        if (selectedCourse && selectedCourse.value) {
            previewBox.classList.remove('hidden');
            const isPublishable = selectedCourse.dataset.publishable === 'true';
            const statusText = selectedCourse.dataset.status || 'Draft';

            previewModules.textContent = selectedCourse.dataset.modules || '0';
            previewDuration.textContent = selectedCourse.dataset.duration || '0';
            previewStatus.textContent = statusText;

            if (isPublishable) {
                previewStatus.className = 'px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800';
                previewNote.textContent = '✓ All modules and quizzes in this course are ready for publishing.';
                previewNote.className = 'text-[11px] text-emerald-700 font-medium pt-1';
            } else {
                previewStatus.className = 'px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800';
                previewNote.textContent = 'ℹ Course is currently in Draft or missing published module quizzes. You can save this workshop as a draft now and publish later.';
                previewNote.className = 'text-[11px] text-amber-700 pt-1';
            }
        } else {
            previewBox.classList.add('hidden');
        }
    }

    workshopCourse.addEventListener('change', updateWorkshopCourseState);
    updateWorkshopCourseState();
</script>
