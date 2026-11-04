<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CertificateApprovalController extends Controller
{
    public function index(): View
    {
        $pendingCertificates = Certification::where('status', 'pending')
            ->with(['teacher', 'workshop', 'assessmentAttempt'])
            ->latest('certified_at')
            ->get();

        return view('trainer.certificates.pending', compact('pendingCertificates'));
    }

    public function approve(Request $request, Certification $certification): RedirectResponse
    {
        $updated = Certification::whereKey($certification->id)
            ->where('status', 'pending')
            ->update([
                'status' => 'approved',
                'approved_by' => $request->user()->id,
                'approved_at' => now(),
            ]);

        if (! $updated) {
            return back()->with('info', 'This certificate has already been approved.');
        }

        return back()->with('success', 'Certificate approved and signed.');
    }
}
