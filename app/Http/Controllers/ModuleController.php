<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModuleController extends Controller
{
    public function create($courseId)
    {
        $course = Course::where('trainer_id', auth()->id())
            ->findOrFail($courseId);

        return view('trainer.modules.create', compact('course'));
    }

    public function store(Request $request, $courseId)
    {
        $course = Course::where('trainer_id', auth()->id())
            ->findOrFail($courseId);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'learning_objectives' => ['required', 'string'],
            'estimated_duration' => ['required', 'integer', 'min:1'],
            'sequence' => ['required', 'integer', 'min:1'],
            'is_required' => ['nullable', 'boolean'],
        ]);

        $validated['course_id'] = $course->id;
        $validated['is_required'] = $request->has('is_required');

        DB::transaction(function () use ($validated, $course) {
            $module = Module::create($validated);

            $module->assessments()->create([
                'workshop_id' => null,
                'trainer_id' => $course->trainer_id,
                'title' => 'Module Quiz: '.$module->title,
                'description' => 'Complete this quiz to pass the module and unlock the next one.',
                'passing_score' => 75,
                'time_limit' => 30,
                'max_attempts' => 3,
                'is_published' => false,
            ]);

            if ($course->status === 'published') {
                $course->update(['status' => 'draft']);
            }
        });

        return redirect()
            ->route('trainer.courses.show', $course->id)
            ->with('success', 'Module created successfully.');
    }

    public function edit($courseId, $moduleId)
    {
        $course = Course::where('trainer_id', auth()->id())
            ->findOrFail($courseId);

        $module = Module::where('course_id', $course->id)
            ->findOrFail($moduleId);

        return view(
            'trainer.modules.edit',
            compact('course', 'module')
        );
    }

    public function update(Request $request, $courseId, $moduleId)
    {
        $course = Course::where('trainer_id', auth()->id())
            ->findOrFail($courseId);

        $module = Module::where('course_id', $course->id)
            ->findOrFail($moduleId);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'learning_objectives' => ['required', 'string'],
            'estimated_duration' => ['required', 'integer', 'min:1'],
            'sequence' => ['required', 'integer', 'min:1'],
            'is_required' => ['nullable', 'boolean'],
        ]);

        $validated['is_required'] = $request->has('is_required');

        $module->update($validated);

        return redirect()
            ->route('trainer.courses.show', $course->id)
            ->with('success', 'Module updated successfully.');
    }

    public function destroy($courseId, $moduleId)
    {
        $course = Course::where('trainer_id', auth()->id())
            ->findOrFail($courseId);

        $module = Module::where('course_id', $course->id)
            ->findOrFail($moduleId);

        $module->delete();

        if ($course->status === 'published' && ! $course->hasReadyModuleQuizzes()) {
            $course->update(['status' => 'draft']);
        }

        return redirect()
            ->route('trainer.courses.show', $course->id)
            ->with('success', 'Module deleted successfully.');
    }
}