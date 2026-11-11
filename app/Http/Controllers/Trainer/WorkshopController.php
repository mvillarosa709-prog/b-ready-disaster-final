<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\ClassroomMaterial;
use App\Models\ClassroomPackage;
use App\Models\Course;
use App\Models\Workshop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WorkshopController extends Controller
{
    public function index(): View
    {
        $workshops = Workshop::where('trainer_id', auth()->id())
            ->with('course')
            ->withCount('workshopTeachers')
            ->latest('start_date')
            ->get();

        return view('trainer.workshops.index', compact('workshops'));
    }

    public function show(Workshop $workshop): View
    {
        $this->authorizeTrainer($workshop);

        $workshop->load([
            'course.modules.quiz.questions',
            'workshopTeachers.teacher',
            'certifications.teacher',
            'classroomPackage.materials',
        ]);

        $canPublish = $workshop->course ? $this->canPublishWorkshopFor($workshop->course) : false;

        return view('trainer.workshops.show', compact('workshop', 'canPublish'));
    }

    public function create(): View
    {
        $courses = $this->availableCourses();

        return view('trainer.workshops.create', compact('courses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules(isCreating: true));
        $course = Course::where('trainer_id', $request->user()->id)
            ->find($validated['course_id']);

        if (! $course) {
            return back()->withInput()->withErrors([
                'course_id' => 'Select a course that belongs to your trainer account.',
            ]);
        }

        $status = $validated['status'] ?? 'draft';
        if ($status === 'published' && ! $this->canPublishWorkshopFor($course)) {
            return back()->withInput()->withErrors([
                'status' => 'Publish the selected course and complete and publish its module quizzes before publishing the workshop.',
            ]);
        }

        $workshop = Workshop::create([
            ...$validated,
            'status' => $status,
            'trainer_id' => $request->user()->id,
        ]);

        if ($status === 'published') {
            $this->ensureClassroomPackage($workshop);
        }

        return redirect()
            ->route('trainer.workshops.index')
            ->with('success', 'Workshop created successfully.');
    }

    public function edit(Workshop $workshop): View
    {
        $this->authorizeTrainer($workshop);
        $courses = $this->availableCourses();

        return view('trainer.workshops.edit', compact('workshop', 'courses'));
    }

    public function update(Request $request, Workshop $workshop): RedirectResponse
    {
        $this->authorizeTrainer($workshop);
        $validated = $request->validate($this->rules(isCreating: false));
        $course = Course::where('trainer_id', $request->user()->id)
            ->find($validated['course_id']);

        if (! $course) {
            return back()->withInput()->withErrors([
                'course_id' => 'Select a course that belongs to your trainer account.',
            ]);
        }

        if (in_array($workshop->status, ['published', 'ongoing'], true) && $validated['status'] === 'draft') {
            return back()->withInput()->withErrors([
                'status' => 'Published workshops cannot be switched back to draft.',
            ]);
        }

        if ($validated['status'] === 'published' && ! $this->canPublishWorkshopFor($course)) {
            return back()->withInput()->withErrors([
                'status' => 'Publish the selected course and complete and publish its module quizzes before publishing the workshop.',
            ]);
        }

        $workshop->update($validated);

        if ($validated['status'] === 'published') {
            $this->ensureClassroomPackage($workshop);
        }

        return redirect()
            ->route('trainer.workshops.show', $workshop)
            ->with('success', 'Workshop updated successfully.');
    }

    public function publish(Workshop $workshop): RedirectResponse
    {
        $this->authorizeTrainer($workshop);

        if (! $workshop->course || ! $this->canPublishWorkshopFor($workshop->course)) {
            return back()->withErrors([
                'status' => 'Publish the selected course and complete and publish its module quizzes before publishing the workshop.',
            ]);
        }

        $workshop->update(['status' => 'published']);
        $this->ensureClassroomPackage($workshop);

        return redirect()
            ->route('trainer.workshops.show', $workshop)
            ->with('success', 'Workshop published! Teachers can now view and enroll in Discover Workshops.');
    }

    public function savePackage(Request $request, Workshop $workshop): RedirectResponse
    {
        $this->authorizeTrainer($workshop);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        ClassroomPackage::updateOrCreate(
            ['workshop_id' => $workshop->id],
            [
                'trainer_id' => $workshop->trainer_id,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'is_published' => $request->boolean('is_published', true),
            ]
        );

        return redirect()
            ->route('trainer.workshops.show', $workshop)
            ->with('success', 'Classroom package saved.');
    }

    public function storePackageMaterial(Request $request, Workshop $workshop): RedirectResponse
    {
        $this->authorizeTrainer($workshop);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'material_type' => ['required', Rule::in([
                'student_manual',
                'teacher_guide',
                'classroom_activity',
                'worksheet',
                'student_assessment',
                'answer_key',
                'other',
            ])],
            'description' => ['nullable', 'string', 'max:2000'],
            'file' => ['required', 'file', 'max:20480'],
        ]);

        $package = $this->ensureClassroomPackage($workshop);
        $uploaded = $request->file('file');
        $storedPath = $uploaded->store('classroom_packages', 'public');
        $nextSequence = ($package->materials()->max('sequence') ?? 0) + 1;

        $package->materials()->create([
            'title' => $validated['title'],
            'material_type' => $validated['material_type'],
            'file_path' => $storedPath,
            'original_filename' => $uploaded->getClientOriginalName(),
            'description' => $validated['description'] ?? null,
            'sequence' => $nextSequence,
        ]);

        return redirect()
            ->route('trainer.workshops.show', $workshop)
            ->with('success', 'Classroom material added to package.');
    }

    public function destroyPackageMaterial(Workshop $workshop, ClassroomMaterial $material): RedirectResponse
    {
        $this->authorizeTrainer($workshop);

        $package = $workshop->classroomPackage;
        abort_unless($package && (int) $material->classroom_package_id === (int) $package->id, 404);

        if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
            Storage::disk('public')->delete($material->file_path);
        }

        $material->delete();

        return redirect()
            ->route('trainer.workshops.show', $workshop)
            ->with('success', 'Classroom material removed.');
    }

    public function destroy(Workshop $workshop): RedirectResponse
    {
        $this->authorizeTrainer($workshop);

        $hasLearnerRecords = $workshop->workshopTeachers()->exists()
            || $workshop->certifications()->exists()
            || $workshop->moduleProgress()->exists()
            || $workshop->assessments()->whereHas('attempts')->exists();

        if ($hasLearnerRecords) {
            return back()->withErrors([
                'workshop' => 'This workshop has learner records and cannot be deleted. Change its status to draft instead.',
            ]);
        }

        $workshop->delete();

        return redirect()
            ->route('trainer.workshops.index')
            ->with('success', 'Workshop deleted successfully.');
    }

    private function rules(bool $isCreating = false): array
    {
        return [
            'course_id' => ['required', 'integer'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'registration_deadline' => ['required', 'date', 'before_or_equal:start_date'],
            'status' => $isCreating ? ['nullable', Rule::in(['draft', 'published'])] : ['required', Rule::in(['draft', 'published'])],
        ];
    }

    private function availableCourses()
    {
        return Course::where('trainer_id', auth()->id())
            ->withCount('modules')
            ->with('modules')
            ->orderBy('title')
            ->get()
            ->each(fn (Course $course) => $course->setAttribute(
                'workshop_publishable',
                $this->canPublishWorkshopFor($course)
            ));
    }

    private function canPublishWorkshopFor(Course $course): bool
    {
        return $course->status === 'published' && $course->hasReadyModuleQuizzes();
    }

    private function ensureClassroomPackage(Workshop $workshop): ClassroomPackage
    {
        return ClassroomPackage::firstOrCreate(
            ['workshop_id' => $workshop->id],
            [
                'trainer_id' => $workshop->trainer_id,
                'title' => $workshop->title . ' — Classroom Package',
                'description' => 'Classroom teaching toolkit and implementation guides unlocked upon certification.',
                'is_published' => true,
            ]
        );
    }

    private function authorizeTrainer(Workshop $workshop): void
    {
        abort_unless((int) $workshop->trainer_id === (int) auth()->id(), 404);
    }
}
