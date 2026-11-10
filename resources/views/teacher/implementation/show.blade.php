<x-dynamic-component component="layouts.teacher">
    <x-slot name="title">Implementation Analytics — {{ $implementation->workshop->title }}</x-slot>

    <!-- Header Banner (Matches Trainer Side) -->
    <div class="bg-white border-b border-slate-200/80 px-4 py-6 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2">
                    <a href="{{ route('teacher.implementations.index') }}" class="hover:text-emerald-600 transition font-medium">
                        Classroom Implementations
                    </a>
                    <span>/</span>
                    <span class="text-slate-800 font-semibold">Report Details</span>
                </nav>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Implementation Report</h1>
                <p class="text-xs text-slate-500 mt-1">Conducted on <strong class="text-slate-700">{{ $implementation->implementation_date->format('F d, Y') }}</strong></p>
            </div>
            <div>
                <a href="{{ route('teacher.implementations.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition shadow-xs">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to All Implementations
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 space-y-6">

        <!-- Implementation Overview Header Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                <div>
                    <span class="inline-block text-[11px] font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-md px-2.5 py-1">
                        {{ $implementation->workshop->title }}
                    </span>
                    <h2 class="mt-2 text-xl sm:text-2xl font-black text-slate-900">
                        {{ $implementation->classroomPackage->title }}
                    </h2>
                </div>

                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 border border-emerald-200 shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    Implementation Completed
                </span>
            </div>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs text-slate-600">
                <div>
                    <span class="text-slate-400 block">Lead Educator:</span>
                    <span class="font-bold text-slate-800">{{ $implementation->teacher->name }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block">Workshop Trainer:</span>
                    <span class="font-bold text-slate-800">{{ $implementation->workshop->trainer->name ?? 'DRR Trainer' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block">Report Recorded:</span>
                    <span class="font-bold text-slate-800">{{ $implementation->created_at->format('M d, Y &bull; h:i A') }}</span>
                </div>
            </div>
        </div>

        <!-- Automated Summary Dashboard (Metric Cards) -->
        <div>
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-3">Automated Performance Aggregates</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <!-- Students Reached -->
                <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Students Reached</span>
                    <div class="mt-2 text-3xl font-extrabold text-slate-900">{{ $analytics['total_students'] }}</div>
                    <span class="text-[11px] text-slate-500 mt-1 block">Participated in drill</span>
                </div>

                <!-- Passing Rate % -->
                <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Passing Rate</span>
                    <div class="mt-2 text-3xl font-extrabold text-emerald-600">{{ $analytics['passing_rate'] }}%</div>
                    <span class="text-[11px] text-emerald-700 font-medium mt-1 block">
                        {{ $analytics['passed_count'] }} Passed / {{ $analytics['failed_count'] }} Failed
                    </span>
                </div>

                <!-- Class Average -->
                <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Class Average</span>
                    <div class="mt-2 text-3xl font-extrabold text-emerald-600">{{ $analytics['average_percentage'] }}%</div>
                    <span class="text-[11px] text-slate-500 mt-1 block">Mean score: {{ $analytics['average_score'] }}</span>
                </div>

                <!-- High / Low Scores -->
                <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Score Spread</span>
                    <div class="mt-2 text-2xl font-extrabold text-slate-900">
                        {{ $analytics['highest_score'] }} <span class="text-xs font-normal text-slate-400">high</span> &bull; {{ $analytics['lowest_score'] }} <span class="text-xs font-normal text-slate-400">low</span>
                    </div>
                    <span class="text-[11px] text-slate-500 mt-1 block">Assessment performance</span>
                </div>
            </div>
        </div>

        <!-- Qualitative Reflection & Supporting Record -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-4">
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">
                Teacher Reflection &amp; Field Notes
            </h3>

            <div class="space-y-3">
                <div>
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Teacher Reflection</h4>
                    <p class="mt-1 text-xs sm:text-sm text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-100 whitespace-pre-line">
                        {{ $implementation->teacher_reflection ?? 'No detailed reflection notes recorded.' }}
                    </p>
                </div>

                @if ($implementation->remarks)
                    <div>
                        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Remarks & Recommendations</h4>
                        <p class="mt-1 text-xs text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100">
                            {{ $implementation->remarks }}
                        </p>
                    </div>
                @endif

                @if ($implementation->supporting_record)
                    <div class="pt-2 flex items-center justify-between text-xs">
                        <span class="text-slate-500">Supporting Documentation Attachment:</span>
                        <a href="{{ Storage::disk('public')->url($implementation->supporting_record) }}" target="_blank"
                           class="inline-flex items-center gap-1.5 font-bold text-emerald-600 hover:text-emerald-800">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                            View Uploaded Record
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Student Results Breakdown Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Student Results Matrix</h3>
                    <p class="text-xs text-slate-500">Individual student scores and pass/fail accreditation evaluations.</p>
                </div>
                <span class="text-xs font-bold text-slate-600 bg-slate-100 px-3 py-1 rounded-full">
                    {{ $implementation->studentResults->count() }} Records
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200/80">
                        <tr>
                            <th class="p-4 w-16 text-center">#</th>
                            <th class="p-4">Student Identifier</th>
                            <th class="p-4 text-center">Score</th>
                            <th class="p-4 text-center">Percentage</th>
                            <th class="p-4 text-right">Result</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach ($implementation->studentResults as $index => $res)
                            @php
                                $isPassed = $res->result === 'passed';
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="p-4 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                                <td class="p-4 font-bold text-slate-900">{{ $res->student_identifier }}</td>
                               <td class="p-4 text-center font-medium">{{ $res->score }} / {{ $res->total_questions }}</td>
                                <td class="p-4 text-center font-extrabold text-slate-800">{{ $res->percentage }}%</td>
                                <td class="p-4 text-right">
                                    <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $isPassed ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                        {{ ucfirst($res->result) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-dynamic-component>
