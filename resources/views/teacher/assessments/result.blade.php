<x-dynamic-component component="layouts.teacher">
    <x-slot name="title">Assessment Results — {{ $workshop->title }}</x-slot>

    <!-- Header Banner (Matches Trainer Side) -->
    <div class="bg-white border-b border-slate-200/80 px-4 py-6 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Quiz Results</h1>
                <p class="text-xs text-slate-500 mt-1">{{ $workshop->title }}</p>
            </div>
            <div>
                <a href="{{ route('teacher.workshops.show', $workshop) }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition shadow-xs">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Workshop
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 space-y-6">
        @php
            $isPassed = $attempt->result === 'passed';
            $passingScore = $attempt->assessment->passing_score;
        @endphp

        <!-- Main Results Banner -->
        <div class="bg-white rounded-2xl border p-8 shadow-xs text-center space-y-4 {{ $isPassed ? 'border-emerald-200' : 'border-rose-200' }}">
            <!-- Icon -->
            <div class="mx-auto h-16 w-16 rounded-full flex items-center justify-center {{ $isPassed ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600' }}">
                @if ($isPassed)
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                @else
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                @endif
            </div>

            <!-- Result Label -->
            <div>
                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $isPassed ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                    {{ $isPassed ? 'Assessment Passed' : 'Assessment Failed' }}
                </span>
                <h2 class="mt-3 text-3xl font-black text-slate-900">
                    {{ $attempt->percentage }}%
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Score: <strong>{{ $attempt->score }}</strong> / {{ $attempt->total_points }} points &bull; Required to pass: <strong>{{ $passingScore }}%</strong>
                </p>
            </div>

            <!-- Descriptive Text -->
            <p class="text-sm text-slate-600 max-w-lg mx-auto leading-relaxed">
                @if ($isPassed)
                    Congratulations! You have passed the assessment. Your certificate is awaiting DRR Trainer verification and signature.
                @else
                    You did not meet the {{ $passingScore }}% passing requirement on Attempt #{{ $attempt->attempt_number }}. Your answers and the answer key are available only to your trainer.
                @endif
            </p>

            <!-- Actions -->
            <div class="pt-4 flex flex-wrap justify-center gap-3">
                @if ($isPassed)
                    <a href="{{ route('teacher.assessments.certificate', $workshop) }}" target="_blank"
                       class="inline-flex items-center gap-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs px-5 py-2.5 shadow-xs transition-colors">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z"/>
                        </svg>
                        View Official Certificate
                    </a>

                    @if ($workshop->classroomPackage && $certification?->status === 'approved')
                        <a href="{{ route('teacher.packages.show', $workshop->classroomPackage) }}"
                           class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-5 py-2.5 shadow-xs transition-colors">
                            Open Workshop Repository &rarr;
                        </a>
                    @endif
                @else
                    @if ($attemptsLeft > 0)
                        @if ($attempt->assessment->module_id)
                            <a href="{{ route('teacher.learning.module', [$workshop, $attempt->assessment->module_id]) }}"
                           class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-5 py-2.5 shadow-xs transition-colors">
                            Retake Quiz ({{ $attemptsLeft }} {{ Str::plural('attempt', $attemptsLeft) }} left)
                            </a>
                        @else
                            <a href="{{ route('teacher.workshops.show', $workshop) }}"
                               class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-5 py-2.5 shadow-xs transition-colors">
                                Return to Workshop Modules
                            </a>
                        @endif
                    @else
                        <span class="inline-block text-xs font-semibold text-rose-600 bg-rose-50 px-3 py-2 rounded-xl border border-rose-200">
                            No remaining attempts left. Contact trainer for support.
                        </span>
                    @endif
                    <a href="{{ route('teacher.workshops.show', $workshop) }}"
                       class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white text-slate-700 font-bold text-xs px-4 py-2.5 hover:bg-slate-50 transition-colors">
                        Review Syllabus
                    </a>
                @endif
            </div>
        </div>

    </div>
</x-dynamic-component>
