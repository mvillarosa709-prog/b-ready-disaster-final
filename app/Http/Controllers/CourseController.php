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
            'status' => ['required', 'in:draft,published,unpublished'],
        ]);

        $validated['trainer_id'] = auth()->id();

        Course::create($validated);

        return redirect()
            ->route('trainer.courses.index')
            ->with('success', 'Course created successfully.');
    }

    /**
     * Display a specific course.
     */
    public function show($id)
    {
        $course = Course::where('trainer_id', auth()->id())
            ->with('modules')
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
            'status' => ['required', 'in:draft,published,unpublished'],
        ]);

        $course->update($validated);

        return redirect()
            ->route('trainer.courses.show', $course->id)
            ->with('success', 'Course updated successfully.');
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