<x-layouts.admin>
    <x-slot name="title">Platform Analytics & Reports — Super Admin</x-slot>

    <!-- Header Banner -->
    <div class="bg-white border-b border-slate-200/80">
        <div class="w-full px-4 py-7 sm:px-6 lg:px-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                        EXECUTIVE REPORTING
                    </span>
                    <span class="text-xs text-slate-500">• Institutional DRR Intelligence</span>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 mt-1 tracking-tight">System Performance & Impact Analytics</h1>
                <p class="text-sm text-slate-500 mt-1">Cross-workshop assessment results, teacher certification throughput, and classroom drill reach.</p>
            </div>
        </div>
    </div>

    <div class="w-full px-4 sm:px-6 lg:px-8 mt-6 space-y-6">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Assessment Takers</span>
                <p class="text-3xl font-black text-slate-900 mt-2">{{ $totalTakers }}</p>
                <p class="text-[11px] text-slate-500 mt-1">{{ $passedCount }} passed successfully</p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Assessment Pass Rate</span>
                <p class="text-3xl font-black text-emerald-600 mt-2">{{ $passingRate }}%</p>
                <p class="text-[11px] text-slate-500 mt-1">Standard benchmark &ge; 75%</p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Certificates Issued</span>
                <p class="text-3xl font-black text-indigo-600 mt-2">{{ $certifications->where('status', 'approved')->count() }}</p>
                <p class="text-[11px] text-slate-500 mt-1">{{ $certifications->where('status', 'pending')->count() }} pending sign-off</p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Students Reached</span>
                <p class="text-3xl font-black text-sky-600 mt-2">{{ $totalStudentsImpacted }}+</p>
                <p class="text-[11px] text-slate-500 mt-1">Via verified classroom drills</p>
            </div>
        </div>

        <!-- Recent Attempts & Certifications -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
            <!-- Certifications Log -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-bold text-slate-900">Recent Certifications</h3>
                    <span class="text-xs font-bold text-indigo-600">{{ $certifications->count() }} records</span>
                </div>

                <div class="mt-3 divide-y divide-slate-100 text-xs">
                    @forelse ($certifications->take(5) as $cert)
                        <div class="py-3 flex items-center justify-between">
                            <div>
                                <p class="font-bold text-slate-900">{{ $cert->teacher?->name }}</p>
                                <p class="text-[11px] text-slate-500">{{ $cert->workshop?->title }}</p>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $cert->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $cert->status }}
                                </span>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ $cert->certified_at ? $cert->certified_at->format('M d, Y') : '' }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="py-4 text-center text-slate-400">No certifications recorded yet.</p>
                    @endforelse
                </div>
            </div>

            <!-- Classroom Implementations Log -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-bold text-slate-900">Classroom Drill Implementations</h3>
                    <span class="text-xs font-bold text-indigo-600">{{ $implementations->count() }} conducted</span>
                </div>

                <div class="mt-3 divide-y divide-slate-100 text-xs">
                    @forelse ($implementations->take(5) as $imp)
                        <div class="py-3 flex items-center justify-between">
                            <div>
                                <p class="font-bold text-slate-900">{{ $imp->teacher?->name }}</p>
                                <p class="text-[11px] text-slate-500">Grade {{ $imp->grade_level }} &bull; {{ $imp->section_name }}</p>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-sky-100 text-sky-800">
                                    {{ $imp->attendees_count }} Students
                                </span>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ $imp->conducted_at ? \Carbon\Carbon::parse($imp->conducted_at)->format('M d, Y') : '' }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="py-4 text-center text-slate-400">No classroom implementations logged yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
