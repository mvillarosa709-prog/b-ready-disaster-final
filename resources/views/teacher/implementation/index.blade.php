<x-dynamic-component component="layouts.teacher">
    <x-slot name="title">Classroom Implementations</x-slot>
    <x-slot name="header">Classroom Implementation Reports</x-slot>

    <div class="space-y-6">
        <!-- Back Button -->
        <div>
            <a href="{{ url()->previous() }}"
               class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-indigo-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </a>
        </div>

        <!-- Header & Action -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Classroom Teaching Logs & Student Impact</h2>
                <p class="text-xs text-slate-500">Track and review student assessment metrics conducted in your school.</p>
            </div>
            <a href="{{ route('teacher.implementations.create') }}" 
               class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-4 py-2.5 transition-colors shadow-xs">
                + Log New Classroom Implementation
            </a>
        </div>

        <!-- 4 Top Aggregate Summary Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Teaching Sessions</span>
                <div class="mt-2 text-3xl font-extrabold text-slate-900">{{ $totalSessions }}</div>
                <div class="mt-1 text-xs text-slate-400">Classroom rollouts logged</div>
            </div>

            <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Students Reached</span>
                <div class="mt-2 text-3xl font-extrabold text-indigo-600">{{ number_format($totalStudents) }}</div>
                <div class="mt-1 text-xs text-slate-400">Pupils educated in DRR</div>
            </div>

            <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Students Passed</span>
                <div class="mt-2 text-3xl font-extrabold text-emerald-600">{{ number_format($totalPassed) }}</div>
                <div class="mt-1 text-xs text-slate-400">Met passing criteria</div>
            </div>

            <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Overall Pass Rate</span>
                <div class="mt-2 text-3xl font-extrabold text-slate-900">{{ $overallPassRate }}%</div>
                <div class="mt-1 text-xs text-slate-400">Class assessment average</div>
            </div>
        </div>

        <!-- Implementations Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            @if ($implementations->isEmpty())
                <div class="p-12 text-center">
                    <div class="mx-auto h-12 w-12 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="mt-3 text-sm font-bold text-slate-900">No implementation records found</h3>
                    <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto">
                        Once certified in a workshop, conduct a classroom session with your students and record their scores here.
                    </p>
                    <a href="{{ route('teacher.implementations.create') }}" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition-colors">
                        Log First Implementation
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200/80">
                            <tr>
                                <th class="p-4">Implementation Date</th>
                                <th class="p-4">Associated Workshop</th>
                                <th class="p-4">Workshop Repository</th>
                                <th class="p-4 text-center">Students Reached</th>
                                <th class="p-4 text-center">Passed / Failed</th>
                                <th class="p-4 text-center">Pass Rate</th>
                                <th class="p-4 text-right">Actions</th>
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
                                    <td class="p-4 font-bold text-slate-900 whitespace-nowrap">
                                        {{ $imp->implementation_date->format('M d, Y') }}
                                    </td>
                                    <td class="p-4 font-medium text-slate-800">
                                        {{ $imp->workshop->title }}
                                    </td>
                                    <td class="p-4 text-slate-600">
                                        {{ $imp->classroomPackage->title }}
                                    </td>
                                    <td class="p-4 text-center font-bold text-slate-900">
                                        {{ $imp->students_participated }}
                                    </td>
                                    <td class="p-4 text-center whitespace-nowrap">
                                        <span class="inline-block font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                            {{ $imp->students_passed }} Passed
                                        </span>
                                        <span class="inline-block font-semibold text-rose-700 bg-rose-50 px-2 py-0.5 rounded border border-rose-200 ml-1">
                                            {{ $imp->students_failed }} Failed
                                        </span>
                                    </td>
                                    <td class="p-4 text-center font-extrabold text-slate-900">
                                        {{ $rate }}%
                                    </td>
                                    <td class="p-4 text-right whitespace-nowrap">
                                        <a href="{{ route('teacher.implementations.show', $imp) }}" 
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold transition-colors">
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
