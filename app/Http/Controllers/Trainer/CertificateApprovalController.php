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
            ->with(['teacher', 'workshop.course', 'assessmentAttempt'])
            ->latest('certified_at')
            ->get();

        $approvedCertificates = Certification::where('status', 'approved')
            ->with(['teacher', 'workshop.course', 'assessmentAttempt', 'approver'])
            ->latest('approved_at')
            ->latest('certified_at')
            ->get();

        return view('trainer.certificates.pending', compact(
            'pendingCertificates',
            'approvedCertificates'
        ));
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

        return back()->with('success', 'Certificate approved and digitally signed.');
    }
}
