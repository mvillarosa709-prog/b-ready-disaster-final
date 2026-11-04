<?php

namespace App\Http\Middleware;

use App\Models\Certification;
use App\Models\ClassroomPackage;
use App\Models\Workshop;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTeacherCertified
{
    /**
     * Handle an incoming request.
     * Ensure the teacher holds an official certification for the workshop/package.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== 'teacher') {
            abort(403, 'Unauthorized access.');
        }

        $workshopId = null;

        // Check if workshop model or parameter is present
        if ($request->route('workshop')) {
            $workshop = $request->route('workshop');
            $workshopId = $workshop instanceof Workshop ? $workshop->id : (int) $workshop;
        } elseif ($request->route('package')) {
            $package = $request->route('package');
            if ($package instanceof ClassroomPackage) {
                $workshopId = $package->workshop_id;
            } else {
                $pkg = ClassroomPackage::find((int) $package);
                $workshopId = $pkg ? $pkg->workshop_id : null;
            }
        } elseif ($request->filled('workshop_id')) {
            $workshopId = (int) $request->input('workshop_id');
        }

        if ($workshopId) {
            $isCertified = Certification::where(function ($q) use ($user) {
                $q->where('teacher_id', $user->id)->orWhere('user_id', $user->id);
            })
                ->where('workshop_id', $workshopId)
                ->where('status', 'approved')
                ->exists();

            if (! $isCertified) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Workshop repository is locked. You must pass the workshop final assessment to unlock materials.',
                    ], 403);
                }

                return redirect()->route('teacher.workshops.show', $workshopId)
                    ->with('warning', 'Workshop Repository is LOCKED. You must pass the Final Assessment and obtain official certification first.');
            }
        }

        return $next($request);
    }
}
