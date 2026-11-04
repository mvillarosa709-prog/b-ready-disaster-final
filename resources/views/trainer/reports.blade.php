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
                <a href="{{ route('trainer.courses.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50 shadow-xs transition">
                    Manage Courses &amp; Module Quizzes
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 space-y-8">
        <section class="overflow-hidden rounded-2xl border border-amber-200 bg-white shadow-xs">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-amber-100 bg-amber-50/70 px-6 py-4">
                <div>
                    <h2 class="font-bold text-slate-900">Certificates awaiting approval</h2>
                    <p class="mt-0.5 text-xs text-slate-600">Review and sign certificates earned by teachers.</p>
                </div>
                <a href="{{ route('trainer.certificates.pending') }}" class="rounded-lg bg-amber-600 px-3 py-2 text-xs font-bold text-white hover:bg-amber-700">
                    Review all ({{ $pendingCertificates->count() }})
                </a>
            </div>
            @if ($pendingCertificates->isEmpty())
                <p class="px-6 py-5 text-sm text-slate-500">There are no pending certificates.</p>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach ($pendingCertificates->take(5) as $certificate)
                        <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-3 text-sm">
                            <div>
                                <span class="font-semibold text-slate-900">{{ $certificate->teacher->name }}</span>
                                <span class="text-slate-500"> — {{ $certificate->workshop->title }}</span>
                            </div>
                            <form method="POST" action="{{ route('trainer.certificates.approve', $certificate) }}">
                                @csrf
                                <button type="submit" class="text-xs font-bold text-emerald-700 hover:text-emerald-900">Approve &amp; Sign</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

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

        <section id="module-quiz-results" class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-xs">
            <div class="border-b border-slate-100 px-6 py-5">
                <h2 class="text-lg font-bold text-slate-900">Module Quiz Submissions</h2>
                <p class="mt-0.5 text-xs text-slate-500">Submitted answers and answer keys are visible to trainers only.</p>
            </div>
            @if ($moduleQuizAttempts->isEmpty())
                <p class="px-6 py-8 text-sm text-slate-500">No module quiz submissions have been recorded for your courses.</p>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach ($moduleQuizAttempts as $attempt)
                        <article class="px-6 py-5">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <h3 class="font-semibold text-slate-900">{{ $attempt->assessment->module->title }}</h3>
                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $attempt->teacher->name ?? 'Teacher' }} ·
                                        {{ $attempt->assessment->module->course->title }} ·
                                        Attempt #{{ $attempt->attempt_number }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-slate-900">{{ $attempt->score }} points ({{ number_format($attempt->percentage, 1) }}%)</p>
                                    <span class="text-xs font-semibold {{ $attempt->result === 'passed' ? 'text-emerald-700' : 'text-rose-700' }}">
                                        {{ ucfirst($attempt->result) }}
                                    </span>
                                </div>
                            </div>
                            <details class="mt-4">
                                <summary class="cursor-pointer text-sm font-semibold text-indigo-700 hover:text-indigo-900">Review submitted answers</summary>
                                <div class="mt-3 space-y-3">
                                    @foreach ($attempt->answers as $answer)
                                        @php
                                            $correctAnswer = $answer->question->choices->firstWhere('is_correct', true)?->choice_text;
                                        @endphp
                                        <div class="rounded-xl border p-4 {{ $answer->is_correct ? 'border-emerald-200 bg-emerald-50/50' : 'border-rose-200 bg-rose-50/50' }}">
                                            <p class="text-sm font-semibold text-slate-900">{{ $answer->question->question_text }}</p>
                                            <p class="mt-2 text-xs text-slate-700">
                                                <span class="font-semibold">Submitted:</span>
                                                {{ $answer->answer_text ?? $answer->choice?->choice_text ?? 'No answer provided' }}
                                            </p>
                                            <p class="mt-1 text-xs text-slate-700">
                                                <span class="font-semibold">Correct answer:</span>
                                                {{ $correctAnswer ?? 'Not specified' }}
                                            </p>
                                            <p class="mt-1 text-xs font-semibold {{ $answer->is_correct ? 'text-emerald-700' : 'text-rose-700' }}">
                                                {{ $answer->is_correct ? 'Correct' : 'Incorrect' }} · {{ $answer->points_earned }} / {{ $answer->question->points }} points
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            </details>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        <!-- Section 1: Teacher Assessment Results -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Teacher Assessment & Verification Log</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Recorded final exam attempts and qualification status.</p>
                </div>
                <a href="{{ route('trainer.courses.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                    Manage Modules &rarr;
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