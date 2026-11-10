<x-layouts.trainer>
    <x-slot name="title">Certificate Review &amp; Issuance — B-READY</x-slot>

    <!-- Header Banner -->
    <div class="bg-white border-b border-slate-200/80 px-4 py-6 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2">
                    <a href="{{ route('trainer.reports') }}" class="hover:text-emerald-600 transition font-medium">
                        Monitoring &amp; Reports
                    </a>
                    <span>/</span>
                    <span class="text-slate-800 font-semibold">Certificate Review</span>
                </nav>

                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
                        Certificate Approvals &amp; Registry
                    </h1>
                    @if ($pendingCertificates->isNotEmpty())
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                            {{ $pendingCertificates->count() }} Pending
                        </span>
                    @endif
                </div>
                <p class="text-sm text-slate-500 mt-1">
                    Review qualifying teacher assessment results, issue digital signatures, and monitor certified educators.
                </p>
            </div>

            @if (auth()->user()->role === 'trainer')
                <div class="flex items-center gap-2">
                    <a href="{{ route('trainer.reports') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-400 transition shadow-xs">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Reports
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Content Area -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6" x-data="{ tab: '{{ $pendingCertificates->isNotEmpty() ? 'pending' : 'approved' }}' }">

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-900 flex items-center gap-3 shadow-xs">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('info'))
            <div class="rounded-2xl border border-blue-200 bg-blue-50 px-5 py-4 text-sm text-blue-900 flex items-center gap-3 shadow-xs">
                <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-medium">{{ session('info') }}</span>
            </div>
        @endif

        <!-- Summary Metrics -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Awaiting Signature</span>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-slate-900">{{ $pendingCertificates->count() }}</span>
                    <span class="text-xs font-semibold text-amber-600">Pending Review</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Approved &amp; Signed</span>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-slate-900">{{ $approvedCertificates->count() }}</span>
                    <span class="text-xs font-semibold text-emerald-600">Verified Certificates</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Qualified</span>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-slate-900">{{ $pendingCertificates->count() + $approvedCertificates->count() }}</span>
                    <span class="text-xs font-medium text-slate-500">Certified Educators</span>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="flex items-center justify-between border-b border-slate-200">
            <div class="flex gap-6">
                <button
                    type="button"
                    @click="tab = 'pending'"
                    :class="tab === 'pending' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500 hover:text-slate-700'"
                    class="pb-3.5 text-sm font-semibold border-b-2 transition flex items-center gap-2"
                >
                    <span>Pending Approval</span>
                    <span class="px-2 py-0.5 text-xs rounded-full {{ $pendingCertificates->isNotEmpty() ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600' }}">
                        {{ $pendingCertificates->count() }}
                    </span>
                </button>

                <button
                    type="button"
                    @click="tab = 'approved'"
                    :class="tab === 'approved' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500 hover:text-slate-700'"
                    class="pb-3.5 text-sm font-semibold border-b-2 transition flex items-center gap-2"
                >
                    <span>Issued &amp; Verified Registry</span>
                    <span class="px-2 py-0.5 text-xs rounded-full bg-emerald-100 text-emerald-800">
                        {{ $approvedCertificates->count() }}
                    </span>
                </button>
            </div>
        </div>

        <!-- Pending Certificates Tab -->
        <div x-show="tab === 'pending'" class="space-y-6">
            @if ($pendingCertificates->isEmpty())
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-8 sm:p-12 text-center">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center mb-4">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900">All Caught Up! No Pending Certificates</h2>
                    <p class="mt-1.5 text-sm text-slate-500 max-w-md mx-auto">
                        When teachers finish all required workshop modules and pass their module quizzes (75%+), their certificates will appear here for your digital approval.
                    </p>

                    <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-2xl mx-auto text-left">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="text-xs font-bold text-emerald-700">Step 1</span>
                            <p class="text-xs font-semibold text-slate-800 mt-1">Teacher Completes Modules</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Reads lessons &amp; reviews DRR materials.</p>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="text-xs font-bold text-emerald-700">Step 2</span>
                            <p class="text-xs font-semibold text-slate-800 mt-1">Passes Quizzes (75%+)</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">System automatically queues certificate.</p>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="text-xs font-bold text-emerald-700">Step 3</span>
                            <p class="text-xs font-semibold text-slate-800 mt-1">Trainer Digital Signature</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Approve here to release official PDF.</p>
                        </div>
                    </div>

                    @if ($approvedCertificates->isNotEmpty())
                        <button type="button" @click="tab = 'approved'" class="mt-6 inline-flex items-center gap-2 text-xs font-semibold text-emerald-700 hover:text-emerald-800">
                            <span>View {{ $approvedCertificates->count() }} already issued certificate(s)</span>
                            <span>→</span>
                        </button>
                    @endif
                </div>
            @else
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                            <thead class="bg-slate-50/80 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-6 py-3.5">Teacher / Learner</th>
                                    <th class="px-6 py-3.5">Workshop &amp; Course</th>
                                    <th class="px-6 py-3.5">Certificate No.</th>
                                    <th class="px-6 py-3.5">Qualified Date</th>
                                    <th class="px-6 py-3.5 text-right">Approval Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($pendingCertificates as $certificate)
                                    <tr class="hover:bg-slate-50/60 transition">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-full bg-amber-100 text-amber-800 font-bold text-xs flex items-center justify-center shrink-0">
                                                    {{ strtoupper(substr($certificate->teacher?->name ?? 'T', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="font-bold text-slate-900">{{ $certificate->teacher?->name ?? 'Teacher' }}</div>
                                                    <div class="text-xs text-slate-500">{{ $certificate->teacher?->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-slate-800">{{ $certificate->workshop?->title ?? 'Workshop' }}</div>
                                            <div class="text-xs text-slate-500">{{ $certificate->workshop?->course?->title }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 font-mono text-xs font-medium">
                                                {{ $certificate->certificate_number }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-slate-600 text-xs">
                                            {{ $certificate->certified_at?->format('M d, Y') ?? '—' }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <form method="POST" action="{{ route('trainer.certificates.approve', $certificate) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-700 shadow-xs transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Approve &amp; Sign
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- Approved Certificates Tab -->
        <div x-show="tab === 'approved'" class="space-y-6" style="display: none;">
            @if ($approvedCertificates->isEmpty())
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-10 text-center">
                    <h2 class="font-bold text-slate-900">No approved certificates yet</h2>
                    <p class="mt-1 text-sm text-slate-500">Approved and digitally signed certificates will be logged here.</p>
                </div>
            @else
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                            <thead class="bg-slate-50/80 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-6 py-3.5">Certified Teacher</th>
                                    <th class="px-6 py-3.5">Workshop / Credential</th>
                                    <th class="px-6 py-3.5">Certificate Serial</th>
                                    <th class="px-6 py-3.5">Status</th>
                                    <th class="px-6 py-3.5">Certified Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($approvedCertificates as $certificate)
                                    <tr class="hover:bg-slate-50/60 transition">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-800 font-bold text-xs flex items-center justify-center shrink-0">
                                                    {{ strtoupper(substr($certificate->teacher?->name ?? 'T', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="font-bold text-slate-900">{{ $certificate->teacher?->name ?? 'Teacher' }}</div>
                                                    <div class="text-xs text-slate-500">{{ $certificate->teacher?->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-slate-800">{{ $certificate->workshop?->title ?? 'DRR Workshop' }}</div>
                                            <div class="text-xs text-emerald-700 font-medium">{{ $certificate->badge_name ?? 'DRR Certified Educator' }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 font-mono text-xs font-medium">
                                                {{ $certificate->certificate_number }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                Approved &amp; Signed
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-slate-600 text-xs">
                                            {{ ($certificate->approved_at ?? $certificate->certified_at)?->format('M d, Y') ?? '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

    </div>
</x-layouts.trainer>
