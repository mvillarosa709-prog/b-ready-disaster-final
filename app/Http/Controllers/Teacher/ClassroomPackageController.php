<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use App\Models\ClassroomMaterial;
use App\Models\ClassroomPackage;
use App\Models\WorkshopTeacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ClassroomPackageController extends Controller
{
    /**
     * List all classroom packages corresponding to the teacher's workshops.
     */
    public function index(Request $request): View
    {
        $teacher = $request->user();

        $enrolledWorkshopIds = WorkshopTeacher::where(function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
        })
            ->whereIn('status', ['registered', 'completed'])
            ->pluck('workshop_id');

        $certificationsByWorkshop = Certification::where(function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
        })
            ->where('status', 'approved')
            ->get()
            ->keyBy('workshop_id');

        $certifiedWorkshopIds = $certificationsByWorkshop->keys()->toArray();
        $accessibleWorkshopIds = $enrolledWorkshopIds->merge($certifiedWorkshopIds)->unique();

        $packages = ClassroomPackage::whereIn('workshop_id', $accessibleWorkshopIds)
            ->whereHas('workshop.course', fn ($query) => $query->published())
            ->with(['workshop.trainer', 'materials'])
            ->get();

        return view('teacher.packages.index', compact('packages', 'certifiedWorkshopIds', 'certificationsByWorkshop'));
    }

    /**
     * Display a single classroom package with gated/unlocked status.
     * Guard: If uncertified, package is 🔒 LOCKED. Once certified, flips to ✓ UNLOCKED.
     */
    public function show(Request $request, ClassroomPackage $package): View
    {
        Gate::authorize('view', $package);

        $teacher = $request->user();
        $package->load(['workshop.trainer', 'materials']);

        $isUnlocked = Gate::allows('unlock', $package);

        $certification = Certification::where(function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
        })
            ->where('workshop_id', $package->workshop_id)
            ->where('status', 'approved')
            ->first();

        // Categorize materials by type (teacher_guide, student_manual, worksheet, etc.)
        $materialsByType = $package->materials->groupBy('material_type');

        return view('teacher.packages.show', compact('package', 'isUnlocked', 'certification', 'materialsByType'));
    }

    /**
     * Securely preview a classroom material file (e.g. Teacher Guide, Student Manual, Worksheet).
     */
    public function previewMaterial(Request $request, ClassroomPackage $package, ClassroomMaterial $material)
    {
        Gate::authorize('download', $package);

        if ($material->classroom_package_id !== $package->id) {
            abort(404, 'Material not found in this package.');
        }

        if (Storage::disk('public')->exists($material->file_path)) {
            return response()->file(Storage::disk('public')->path($material->file_path));
        }

        return back()->with('info', "Preview for '{$material->title}' is available online. You may download the guide directly.");
    }

    /**
     * Securely download a classroom material file.
     */
    public function downloadMaterial(Request $request, ClassroomPackage $package, ClassroomMaterial $material)
    {
        Gate::authorize('download', $package);

        if ($material->classroom_package_id !== $package->id) {
            abort(404, 'Material not found in this package.');
        }

        if (Storage::disk('public')->exists($material->file_path)) {
            return Storage::disk('public')->download($material->file_path, $material->original_filename ?? $material->title);
        }

        return back()->with('info', "The file '{$material->title}' is being prepared by the system. Download link will be active shortly.");
    }
}
