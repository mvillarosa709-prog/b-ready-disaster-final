@csrf
@if ($workshop)
    @method('PUT')
@endif

<div class="space-y-5">
    <div>
        <label for="title" class="mb-1.5 block text-sm font-semibold text-slate-700">Workshop title</label>
        <input id="title" name="title" value="{{ old('title', $workshop?->title) }}" required maxlength="255" class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
        @error('title')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="course_id" class="mb-1.5 block text-sm font-semibold text-slate-700">Course</label>
        <select id="course_id" name="course_id" required class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            <option value="">Select a course</option>
            @foreach ($courses as $course)
                <option value="{{ $course->id }}" data-publishable="{{ $course->workshop_publishable ? 'true' : 'false' }}" @selected((string) old('course_id', $workshop?->course_id) === (string) $course->id)>
                    {{ $course->title }} — {{ ucfirst($course->status) }}
                </option>
            @endforeach
        </select>
        @error('course_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        @if ($courses->isEmpty())
            <p class="mt-1 text-xs text-amber-700">Create a course first, then return here to schedule its workshop.</p>
        @else
            <p class="mt-1 text-xs text-slate-500">You can save a workshop as a draft for any of your courses. To publish it, the course and all module quizzes must be published.</p>
        @endif
    </div>

    <div>
        <label for="description" class="mb-1.5 block text-sm font-semibold text-slate-700">Description <span class="font-normal text-slate-400">(optional)</span></label>
        <textarea id="description" name="description" rows="3" maxlength="10000" class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('description', $workshop?->description) }}</textarea>
        @error('description')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div class="grid gap-5 sm:grid-cols-3">
        <div>
            <label for="start_date" class="mb-1.5 block text-sm font-semibold text-slate-700">Starts</label>
            <input id="start_date" type="datetime-local" name="start_date" value="{{ old('start_date', $workshop?->start_date?->format('Y-m-d\TH:i')) }}" required class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            @error('start_date')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="end_date" class="mb-1.5 block text-sm font-semibold text-slate-700">Ends</label>
            <input id="end_date" type="datetime-local" name="end_date" value="{{ old('end_date', $workshop?->end_date?->format('Y-m-d\TH:i')) }}" required class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            @error('end_date')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="registration_deadline" class="mb-1.5 block text-sm font-semibold text-slate-700">Registration deadline</label>
            <input id="registration_deadline" type="datetime-local" name="registration_deadline" value="{{ old('registration_deadline', $workshop?->registration_deadline?->format('Y-m-d\TH:i')) }}" required class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            @error('registration_deadline')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label for="status" class="mb-1.5 block text-sm font-semibold text-slate-700">Status</label>
        <select id="status" name="status" required class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            <option value="draft" @selected(old('status', $workshop?->status ?? 'draft') === 'draft')>Draft</option>
            <option value="published" @disabled(! $courses->firstWhere('id', (int) old('course_id', $workshop?->course_id))?->workshop_publishable) @selected(old('status', $workshop?->status) === 'published')>Published</option>
        </select>
        @error('status')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
        <a href="{{ route('trainer.workshops.index') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
        <button type="submit" @disabled($courses->isEmpty()) class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50">
            {{ $workshop ? 'Save changes' : 'Create workshop' }}
        </button>
    </div>
</div>

<script>
    const workshopCourse = document.getElementById('course_id');
    const workshopStatus = document.getElementById('status');
    const publishedStatus = workshopStatus.querySelector('option[value="published"]');

    function updateWorkshopStatusAvailability() {
        const selectedCourse = workshopCourse.selectedOptions[0];
        publishedStatus.disabled = selectedCourse?.dataset.publishable !== 'true';
        if (publishedStatus.disabled && workshopStatus.value === 'published') {
            workshopStatus.value = 'draft';
        }
    }

    workshopCourse.addEventListener('change', updateWorkshopStatusAvailability);
    updateWorkshopStatusAvailability();
</script>
