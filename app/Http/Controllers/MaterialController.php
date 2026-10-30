<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Material;
use App\Models\Module;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function create($courseId, $moduleId)
    {
        $course = Course::where('trainer_id', auth()->id())
            ->findOrFail($courseId);

        $module = Module::where('course_id', $course->id)
            ->findOrFail($moduleId);

        return view(
            'trainer.materials.create',
            compact('course', 'module')
        );
    }

    public function store(Request $request, $courseId, $moduleId)
    {
        $course = Course::where('trainer_id', auth()->id())
            ->findOrFail($courseId);

        $module = Module::where('course_id', $course->id)
            ->findOrFail($moduleId);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => [
                'required',
                'in:pdf,presentation,video,image,external_resource'
            ],
            'description' => ['nullable', 'string'],
            'external_url' => ['nullable', 'url'],
            'file_path' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['module_id'] = $module->id;

        Material::create($validated);

        return redirect()
            ->route(
                'trainer.courses.show',
                $course->id
            )
            ->with('success', 'Material created successfully.');
    }

    public function edit($courseId, $moduleId, $materialId)
    {
        $course = Course::where('trainer_id', auth()->id())
            ->findOrFail($courseId);

        $module = Module::where('course_id', $course->id)
            ->findOrFail($moduleId);

        $material = Material::where('module_id', $module->id)
            ->findOrFail($materialId);

        return view(
            'trainer.materials.edit',
            compact('course', 'module', 'material')
        );
    }

    public function update(
        Request $request,
        $courseId,
        $moduleId,
        $materialId
    ) {
        $course = Course::where('trainer_id', auth()->id())
            ->findOrFail($courseId);

        $module = Module::where('course_id', $course->id)
            ->findOrFail($moduleId);

        $material = Material::where('module_id', $module->id)
            ->findOrFail($materialId);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => [
                'required',
                'in:pdf,presentation,video,image,external_resource'
            ],
            'description' => ['nullable', 'string'],
            'external_url' => ['nullable', 'url'],
            'file_path' => ['nullable', 'string', 'max:255'],
        ]);

        $material->update($validated);

        return redirect()
            ->route(
                'trainer.courses.show',
                $course->id
            )
            ->with('success', 'Material updated successfully.');
    }

    public function destroy($courseId, $moduleId, $materialId)
    {
        $course = Course::where('trainer_id', auth()->id())
            ->findOrFail($courseId);

        $module = Module::where('course_id', $course->id)
            ->findOrFail($moduleId);

        $material = Material::where('module_id', $module->id)
            ->findOrFail($materialId);

        $material->delete();

        return redirect()
            ->route(
                'trainer.courses.show',
                $course->id
            )
            ->with('success', 'Material deleted successfully.');
    }
}