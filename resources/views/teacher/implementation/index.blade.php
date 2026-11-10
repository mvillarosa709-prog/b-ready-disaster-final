<x-dynamic-component component="layouts.teacher">
    <x-slot name="title">Classroom Implementations — B-READY</x-slot>

    <!-- Header Banner (Matches Trainer Side) -->
    <div class="bg-white border-b border-slate-200/80 px-4 py-8 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        Classroom Impact
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2 tracking-tight">
                    Classroom Implementations
                </h1>
                <p class="text-slate-500 text-sm mt-1">
                    Track and review student assessment metrics conducted in your school.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('teacher.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 shadow-xs transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Dashboard
                </a>
                <a href="{{ route('teacher.implementations.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 shadow-sm transition">
                    + Log New Implementation
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 space-y-6">

        <!-- 4 Top Aggregate Summary Metric Cards (Matches Trainer Dashboard) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Teaching Sessions</span>
                <div class="mt-3 text-3xl font-extrabold text-slate-900">{{ $totalSessions }}</div>
                <div class="mt-1 text-xs text-slate-400">Classroom rollouts logged</div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Students Reached</span>
                <div class="mt-3 text-3xl font-extrabold text-slate-900">{{ number_format($totalStudents) }}</div>
                <div class="mt-1 text-xs text-emerald-600 font-semibold">Pupils educated in DRR</div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Students Passed</span>
                <div class="mt-3 text-3xl font-extrabold text-emerald-600">{{ number_format($totalPassed) }}</div>
                <div class="mt-1 text-xs text-slate-400">Met passing criteria</div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Overall Pass Rate</span>
                <div class="mt-3 text-3xl font-extrabold text-slate-900">{{ $overallPassRate }}%</div>
                <div class="mt-1 text-xs text-slate-400">Class assessment average</div>
            </div>
        </div>

        <!-- Implementations Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            @if ($implementations->isEmpty())
                <div class="p-12 text-center">
                    <h3 class="text-base font-bold text-slate-900">No implementation records found</h3>
                    <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto">
                        Once certified in a workshop, conduct a classroom session with your students and record their scores here.
                    </p>
                    <a href="{{ route('teacher.implementations.create') }}" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-xs font-semibold text-white hover:bg-emerald-700 shadow-xs transition">
                        Log First Implementation
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50/80 text-slate-500 font-semibold uppercase tracking-wide border-b border-slate-200/80">
                            <tr>
                                <th class="px-6 py-3.5">Implementation Date</th>
                                <th class="px-6 py-3.5">Associated Workshop</th>
                                <th class="px-6 py-3.5">Workshop Repository</th>
                                <th class="px-6 py-3.5 text-center">Students Reached</th>
                                <th class="px-6 py-3.5 text-center">Passed / Failed</th>
                                <th class="px-6 py-3.5 text-center">Pass Rate</th>
                                <th class="px-6 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @foreach ($implementations as $imp)
                                @php
                                    $rate = $imp->students_completed_assessment > 0
                                        ? round(($imp->students_passed / $imp->students_completed_assessment) * 100, 1)
                                        : 0;
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="px-6 py-4 font-bold text-slate-900 whitespace-nowrap">
                                        {{ $imp->implementation_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 font-medium text-slate-800">
                                        {{ $imp->workshop->title }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">
                                        {{ $imp->classroomPackage->title }}
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-slate-900">
                                        {{ $imp->students_participated }}
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <span class="inline-block font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                            {{ $imp->students_passed }} Passed
                                        </span>
                                        <span class="inline-block font-semibold text-rose-700 bg-rose-50 px-2 py-0.5 rounded border border-rose-200 ml-1">
                                            {{ $imp->students_failed }} Failed
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center font-extrabold text-slate-900">
                                        {{ $rate }}%
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <a href="{{ route('teacher.implementations.show', $imp) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold transition-colors">
                                            <span>Full Analytics</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-100">
                    {{ $implementations->links() }}
                </div>
            @endif
        </div>
    </div>
</x-dynamic-component>
