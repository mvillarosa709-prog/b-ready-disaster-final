<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Workshop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function create(): View
    {
        $courses = $this->availableCourses();

        return view('trainer.workshops.create', compact('courses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $course = Course::where('trainer_id', $request->user()->id)
            ->find($validated['course_id']);

        if (! $course) {
            return back()->withInput()->withErrors([
                'course_id' => 'Select a course that belongs to your trainer account.',
            ]);
        }

        if ($validated['status'] === 'published' && ! $this->canPublishWorkshopFor($course)) {
            return back()->withInput()->withErrors([
                'status' => 'Publish the selected course and complete and publish its module quizzes before publishing the workshop.',
            ]);
        }

        Workshop::create([
            ...$validated,
            'trainer_id' => $request->user()->id,
        ]);

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
        $validated = $request->validate($this->rules());
        $course = Course::where('trainer_id', $request->user()->id)
            ->find($validated['course_id']);

        if (! $course) {
            return back()->withInput()->withErrors([
                'course_id' => 'Select a course that belongs to your trainer account.',
            ]);
        }

        if ($validated['status'] === 'published' && ! $this->canPublishWorkshopFor($course)) {
            return back()->withInput()->withErrors([
                'status' => 'Publish the selected course and complete and publish its module quizzes before publishing the workshop.',
            ]);
        }

        $workshop->update($validated);

        return redirect()
            ->route('trainer.workshops.index')
            ->with('success', 'Workshop updated successfully.');
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

    private function rules(): array
    {
        return [
            'course_id' => ['required', 'integer'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'registration_deadline' => ['required', 'date', 'before_or_equal:start_date'],
            'status' => ['required', Rule::in(['draft', 'published'])],
        ];
    }

    private function availableCourses()
    {
        return Course::where('trainer_id', auth()->id())
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

    private function authorizeTrainer(Workshop $workshop): void
    {
        abort_unless((int) $workshop->trainer_id === (int) auth()->id(), 404);
    }
}
