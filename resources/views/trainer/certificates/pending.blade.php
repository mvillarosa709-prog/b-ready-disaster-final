<x-layouts.trainer>
    <x-slot name="title">Pending Certificates — B-READY</x-slot>

    <div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-emerald-700">Certificate review</p>
                <h1 class="mt-1 text-2xl font-extrabold text-slate-900">Pending Certificates</h1>
                <p class="mt-1 text-sm text-slate-500">Review passing results and approve certificates for digital signature.</p>
            </div>
            @if (auth()->user()->role === 'trainer')
                <a href="{{ route('trainer.reports') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Back to reports
                </a>
            @endif
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif
        @if (session('info'))
            <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">{{ session('info') }}</div>
        @endif

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            @if ($pendingCertificates->isEmpty())
                <div class="p-10 text-center">
                    <h2 class="font-bold text-slate-900">No certificates are waiting for approval</h2>
                    <p class="mt-1 text-sm text-slate-500">New certificates will appear here after a learner completes all required modules.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-3">Learner</th>
                                <th class="px-5 py-3">Workshop</th>
                                <th class="px-5 py-3">Certificate</th>
                                <th class="px-5 py-3">Passed</th>
                                <th class="px-5 py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($pendingCertificates as $certificate)
                                <tr>
                                    <td class="px-5 py-4 font-semibold text-slate-900">{{ $certificate->teacher->name }}</td>
                                    <td class="px-5 py-4 text-slate-700">{{ $certificate->workshop->title }}</td>
                                    <td class="px-5 py-4 font-mono text-xs text-slate-600">{{ $certificate->certificate_number }}</td>
                                    <td class="px-5 py-4 text-slate-600">{{ $certificate->certified_at?->format('M d, Y') ?? '—' }}</td>
                                    <td class="px-5 py-4">
                                        <form method="POST" action="{{ route('trainer.certificates.approve', $certificate) }}">
                                            @csrf
                                            <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-bold text-white hover:bg-emerald-700">
                                                Approve &amp; Sign
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-layouts.trainer>
