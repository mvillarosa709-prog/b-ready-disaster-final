<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkshopOversightController extends Controller
{
    /**
     * Display all workshops with administrative metrics.
     */
    public function index(Request $request): View
    {
        $workshops = Workshop::with(['trainer', 'course', 'teachers', 'classroomPackage'])
            ->withCount(['teachers', 'workshopModules'])
            ->latest()
            ->paginate(12);

        return view('admin.workshops.index', compact('workshops'));
    }

    /**
     * Display a specific workshop details and roster.
     */
    public function show(Workshop $workshop): View
    {
        $workshop->load(['trainer', 'course.modules', 'teachers', 'classroomPackage.materials']);

        return view('admin.workshops.show', compact('workshop'));
    }
}
