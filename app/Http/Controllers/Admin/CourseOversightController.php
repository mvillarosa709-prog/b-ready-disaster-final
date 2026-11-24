<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseOversightController extends Controller
{
    /**
     * Display all courses and curriculum modules.
     */
    public function index(Request $request): View
    {
        $courses = Course::with(['trainer', 'modules.materials'])
            ->withCount(['modules', 'workshops'])
            ->latest()
            ->paginate(12);

        return view('admin.courses.index', compact('courses'));
    }
}
