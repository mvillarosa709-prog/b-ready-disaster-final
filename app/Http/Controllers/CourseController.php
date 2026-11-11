<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display the Trainer's courses.
     */
    public function index()
    {
        $courses = Course::where('trainer_id', auth()->id())
            ->with('modules')
            ->latest()
            ->get();

        return view('trainer.courses.index', compact('courses'));
    }

    /**
     * Show the Create Course form.
     */
    public function create()
    {
        return view('trainer.courses.create');
    }

    /**
     * Store a newly created course.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'learning_objectives' => ['required', 'string'],
            'target_participants' => ['required', 'string'],
            'estimated_duration' => ['required', 'integer', 'min:1'],
        ]);

        $validated['trainer_id'] = auth()->id();
        $validated['status'] = 'draft';

        $course = Course::create($validated);

        return redirect()
            ->route('trainer.modules.create', $course->id)
            ->with('success', 'Course created! Add your first module below.');
    }

    /**
     * Display a specific course.
     */
    public function show($id)
    {
        $course = Course::where('trainer_id', auth()->id())
            ->with(['workshops', 'modules.materials', 'modules.quiz.questions.choices'])
            ->findOrFail($id);

        return view('trainer.courses.show', compact('course'));
    }

    /**
     * Show the Edit Course form.
     */
    public function edit($id)
    {
        $course = Course::where('trainer_id', auth()->id())
            ->findOrFail($id);

        return view('trainer.courses.edit', compact('course'));
    }

    /**
     * Update an existing course.
     */
    public function update(Request $request, $id)
    {
        $course = Course::where('trainer_id', auth()->id())
            ->findOrFail($id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'learning_objectives' => ['required', 'string'],
            'target_participants' => ['required', 'string'],
            'estimated_duration' => ['required', 'integer', 'min:1'],
        ]);

        $course->update($validated);

        return redirect()
            ->route('trainer.courses.show', $course->id)
            ->with('success', 'Course updated successfully.');
    }

    public function publish($id)
    {
        $course = Course::where('trainer_id', auth()->id())
            ->findOrFail($id);

        if (! $course->hasReadyModuleQuizzes()) {
            return back()->withErrors([
                'publication' => 'Add at least one module and publish a valid quiz for every module before publishing this course.',
            ]);
        }

        $course->update(['status' => 'published']);

        return redirect()
            ->route('trainer.courses.show', $course->id)
            ->with('success', 'Course published successfully.');
    }

    public function unpublish($id)
    {
        $course = Course::where('trainer_id', auth()->id())
            ->findOrFail($id);

        if ($course->status !== 'published') {
            return redirect()
                ->route('trainer.courses.show', $course->id)
                ->withErrors(['publication' => 'Only a published course can be unpublished.']);
        }

        $course->update(['status' => 'unpublished']);

        return redirect()
            ->route('trainer.courses.show', $course->id)
            ->with('success', 'Course unpublished successfully.');
    }

    /**
     * Delete a course.
     */
    public function destroy($id)
    {
        $course = Course::where('trainer_id', auth()->id())
            ->findOrFail($id);

        $course->delete();

        return redirect()
            ->route('trainer.courses.index')
            ->with('success', 'Course deleted successfully.');
    }
}
