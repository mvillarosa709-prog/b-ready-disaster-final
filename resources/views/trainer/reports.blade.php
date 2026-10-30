<x-layouts.trainer>
    <x-slot name="title">Trainer Monitoring & Reports — B-READY</x-slot>

    <!-- Header Banner -->
    <div class="bg-white border-b border-slate-200/80 px-4 py-8 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        DRR Monitoring & Evaluation
                    </span>
                    <span class="text-xs text-slate-400">Complete Training-to-Classroom Lifecycle</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2 tracking-tight">
                    Trainer Reports & Outcomes
                </h1>
                <p class="text-slate-500 text-sm mt-1">
                    Monitor teacher participation, assessment mastery, certifications awarded, and actual classroom implementation results.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('trainer.assessments.take') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50 shadow-xs transition">
                    Preview Assessment Exam
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 space-y-8">
        <!-- 4 Key Metrics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Total Enrolled / Takers -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Assessment Takers</span>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="text-3xl font-extrabold text-slate-900">{{ $totalTakers ?? 0 }}</span>
                    <span class="p-2 rounded-xl bg-blue-50 text-blue-600 font-bold text-xs">Total</span>
                </div>
                <p class="text-xs text-slate-400 mt-2">Recorded test submissions</p>
            </div>

            <!-- Total Passed -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Passed Assessments</span>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="text-3xl font-extrabold text-emerald-600">{{ $passedCount ?? 0 }}</span>
                    <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600 font-bold text-xs">Passed</span>
                </div>
                <p class="text-xs text-slate-400 mt-2">Met 75%+ passing score</p>
            </div>

            <!-- Passing Rate -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Overall Pass Rate</span>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="text-3xl font-extrabold text-indigo-600">{{ $passingRate ?? 0 }}%</span>
                    <span class="p-2 rounded-xl bg-indigo-50 text-indigo-600 font-bold text-xs">Rate</span>
                </div>
                <p class="text-xs text-slate-400 mt-2">Training efficacy benchmark</p>
            </div>

            <!-- Students Reached in Classrooms -->
            @php
                $studentsCount = \App\Models\StudentResult::count();
                $implementationsCount = \App\Models\ClassroomImplementation::count();
            @endphp
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Students Reached</span>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="text-3xl font-extrabold text-purple-600">{{ $studentsCount }}</span>
                    <span class="p-2 rounded-xl bg-purple-50 text-purple-600 font-bold text-xs">{{ $implementationsCount }} Classes</span>
                </div>
                <p class="text-xs text-slate-400 mt-2">Classroom implementations</p>
            </div>
        </div>

        <!-- Section 1: Teacher Assessment Results -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Teacher Assessment & Verification Log</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Recorded final exam attempts and qualification status.</p>
                </div>
                <a href="{{ route('trainer.assessments.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                    View Questions Bank &rarr;
                </a>
            </div>

            @if(isset($results) && $results->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50/80 text-slate-500 uppercase text-xs font-semibold">
                            <tr>
                                <th class="px-6 py-3.5 text-left">Teacher / Participant</th>
                                <th class="px-6 py-3.5 text-left">Score Earned</th>
                                <th class="px-6 py-3.5 text-left">Percentage</th>
                                <th class="px-6 py-3.5 text-left">Status</th>
                                <th class="px-6 py-3.5 text-left">Timestamp</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($results as $res)
                                @php
                                    $pct = $res->total_questions > 0 ? round(($res->score / $res->total_questions) * 100, 1) : 0;
                                @endphp
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="px-6 py-4 font-semibold text-slate-900">
                                        {{ $res->teacher_name }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-700 font-medium">
                                        {{ $res->score }} / {{ $res->total_questions }}
                                    </td>
                                    <td class="px-6 py-4 font-bold text-slate-900">
                                        {{ $pct }}%
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($res->is_passed)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                                ✓ PASSED (Certified)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                                ✕ FAILED (Retake)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-500">
                                        {{ $res->created_at ? $res->created_at->format('M d, Y h:i A') : 'N/A' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-10 text-center text-slate-400 text-sm">
                    No teacher assessment attempts recorded yet.
                </div>
            @endif
        </div>

        <!-- Section 2: Classroom Implementation Monitoring (From Capstone Spec) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h2 class="text-lg font-bold text-slate-900">Classroom Implementations & Student Outcomes</h2>
                <p class="text-xs text-slate-500 mt-0.5">Tracking how certified teachers conduct disaster preparedness drills in actual classes.</p>
            </div>

            @php
                $implementations = \App\Models\ClassroomImplementation::with(['teacher', 'workshop', 'studentResults'])->latest()->get();
            @endphp

            @if($implementations->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50/80 text-slate-500 uppercase text-xs font-semibold">
                            <tr>
                                <th class="px-6 py-3.5 text-left">Teacher Facilitator</th>
                                <th class="px-6 py-3.5 text-left">Workshop</th>
                                <th class="px-6 py-3.5 text-left">Students Reached</th>
                                <th class="px-6 py-3.5 text-left">Pass / Fail</th>
                                <th class="px-6 py-3.5 text-left">Class Average</th>
                                <th class="px-6 py-3.5 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($implementations as $imp)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="px-6 py-4 font-semibold text-slate-900">
                                        {{ $imp->teacher->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">
                                        {{ $imp->workshop->title ?? 'DRR Workshop' }}
                                    </td>
                                    <td class="px-6 py-4 font-bold text-slate-800">
                                        {{ $imp->students_reached ?? $imp->studentResults->count() }}
                                    </td>
                                    <td class="px-6 py-4 text-xs font-semibold">
                                        <span class="text-emerald-700">{{ $imp->assessment_passed_count ?? 0 }} passed</span> / 
                                        <span class="text-red-600">{{ $imp->assessment_failed_count ?? 0 }} failed</span>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-indigo-700">
                                        {{ number_format($imp->class_average_score ?? 0, 1) }}%
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                            {{ ucfirst($imp->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-10 text-center text-slate-400 text-sm">
                    No classroom implementation records submitted by teachers yet. Once teachers pass their assessment and submit student outcomes, they will automatically reflect here.
                </div>
            @endif
        </div>
    </div>
</x-layouts.trainer>