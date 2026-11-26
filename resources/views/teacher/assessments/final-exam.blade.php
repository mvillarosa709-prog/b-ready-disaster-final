<x-dynamic-component component="layouts.teacher">
    <x-slot name="title">Final Examination — {{ $workshop->title }}</x-slot>

    <!-- Header Banner (Full Width) -->
    <div class="bg-white border-b border-slate-200/80">
        <div class="w-full px-4 py-6 sm:px-6 lg:px-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2">
                    <a href="{{ route('teacher.workshops.show', $workshop) }}" class="hover:text-emerald-600 transition font-medium">
                        {{ $workshop->title }}
                    </a>
                    <span>/</span>
                    <span class="text-slate-800 font-semibold">Final Examination</span>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ $assessment->title }}</h1>
            </div>
            <div>
                <a href="{{ route('teacher.workshops.show', $workshop) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition shadow-xs">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Workshop
                </a>
            </div>
        </div>
    </div>

    <div class="w-full px-4 sm:px-6 lg:px-8 mt-6 space-y-6">
        <!-- Exam Overview Banner -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-2">
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-md px-2.5 py-1">
                        ✓ All Modules Completed &bull; Final Examination Unlocked
                    </span>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">
                        {{ $assessment->title }}
                    </h2>
                    <p class="text-sm text-slate-600 leading-relaxed max-w-3xl">
                        {{ $assessment->description ?? 'Complete this final examination to earn your DRR Teacher Certification and unlock the Workshop Repository teaching materials.' }}
                    </p>
                </div>

                <div class="grid grid-cols-3 gap-3 shrink-0 bg-slate-50 p-4 rounded-2xl border border-slate-200/80 text-center">
                    <div class="px-3">
                        <div class="text-[11px] font-semibold uppercase text-slate-400">Passing Score</div>
                        <div class="text-lg font-extrabold text-emerald-700 mt-0.5">{{ $assessment->passing_score }}%</div>
                    </div>
                    <div class="px-3 border-x border-slate-200">
                        <div class="text-[11px] font-semibold uppercase text-slate-400">Questions</div>
                        <div class="text-lg font-extrabold text-slate-900 mt-0.5">{{ $assessment->questions->count() }}</div>
                    </div>
                    <div class="px-3">
                        <div class="text-[11px] font-semibold uppercase text-slate-400">Attempts Left</div>
                        <div class="text-lg font-extrabold text-slate-900 mt-0.5">{{ $attemptsLeft }} / {{ $assessment->max_attempts }}</div>
                    </div>
                </div>
            </div>
        </div>

        @if ($latestAttempt)
            <div class="rounded-2xl border p-5 {{ $latestAttempt->result === 'passed' ? 'border-emerald-200 bg-emerald-50 text-emerald-900' : 'border-rose-200 bg-rose-50 text-rose-900' }}">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-base">
                            Latest Attempt (#{{ $latestAttempt->attempt_number }}): {{ ucfirst($latestAttempt->result) }} — {{ number_format($latestAttempt->percentage, 2) }}%
                        </h3>
                        <p class="text-xs mt-1">
                            Score: {{ $latestAttempt->score }} points &bull; Passing requirement: {{ $assessment->passing_score }}%
                        </p>
                    </div>
                    @if ($certification)
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('teacher.assessments.certificate', $workshop) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold shadow-xs transition">
                                View Official Certificate
                            </a>
                            @if ($workshop->classroomPackage)
                                <a href="{{ route('teacher.packages.show', $workshop->classroomPackage) }}"
                                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition">
                                    Open Workshop Repository &rarr;
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endif

        @if ($certification || $latestAttempt?->result === 'passed')
            <div class="bg-white rounded-2xl border border-emerald-200 p-8 text-center space-y-4 shadow-xs">
                <div class="mx-auto h-14 w-14 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h3 class="text-xl font-extrabold text-slate-900">Final Examination Passed &amp; Repository Unlocked!</h3>
                <p class="text-sm text-slate-600 max-w-lg mx-auto">
                    You have successfully passed the Final Examination for <strong>{{ $workshop->title }}</strong>. Your Workshop Repository and Classroom Teaching Toolkit are now unlocked.
                </p>
                <div class="flex flex-wrap justify-center gap-3 pt-2">
                    @if ($workshop->classroomPackage)
                        <a href="{{ route('teacher.packages.show', $workshop->classroomPackage) }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition">
                            Open Workshop Repository &rarr;
                        </a>
                    @endif
                    <a href="{{ route('teacher.assessments.certificate', $workshop) }}" target="_blank"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold shadow-xs transition">
                        View / Print Certificate
                    </a>
                </div>
            </div>
        @elseif ($attemptsLeft <= 0)
            <div class="bg-white rounded-2xl border border-rose-200 p-8 text-center space-y-3 shadow-xs">
                <h3 class="text-lg font-bold text-rose-800">No Remaining Attempts</h3>
                <p class="text-xs text-slate-600">You have used all {{ $assessment->max_attempts }} attempts for this Final Examination. Please contact your trainer for assistance.</p>
            </div>
        @else
            <form method="POST" action="{{ route('teacher.assessments.final.submit', $workshop) }}" class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-6">
                @csrf

                <div class="space-y-5">
                    @foreach ($assessment->questions as $index => $question)
                        <fieldset class="rounded-2xl border border-slate-200 p-5 hover:border-emerald-200 transition">
                            <legend class="px-2 text-sm sm:text-base font-bold text-slate-900">
                                Question {{ $index + 1 }}: {{ $question->question_text }}
                                <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded ml-1">
                                    {{ $question->points }} pts
                                </span>
                            </legend>

                            @if ($question->question_type === 'fill_blank')
                                <input type="text" name="answers[{{ $question->id }}]" maxlength="1000" required
                                       value="{{ old('answers.' . $question->id) }}"
                                       placeholder="Type your answer"
                                       class="mt-3 block w-full rounded-xl border-slate-300 text-sm shadow-2xs focus:border-emerald-500 focus:ring-emerald-500">
                            @else
                                <div class="mt-3 space-y-2.5">
                                    @foreach ($question->choices as $choice)
                                        <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-3.5 text-sm hover:bg-emerald-50/40 hover:border-emerald-300 cursor-pointer transition">
                                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $choice->id }}"
                                                   @checked((string) old('answers.' . $question->id) === (string) $choice->id)
                                                   required class="mt-0.5 text-emerald-600 focus:ring-emerald-500">
                                            <span class="text-slate-800 font-medium">{{ $choice->choice_text }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </fieldset>
                    @endforeach
                </div>

                @error('answers')
                    <p class="text-sm text-rose-600 font-semibold">{{ $message }}</p>
                @enderror

                <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                    <span class="text-xs text-slate-500">
                        Passing this Final Examination immediately unlocks the Workshop Repository &amp; Official Certificate.
                    </span>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-6 py-3 text-sm font-bold text-white shadow-sm transition cursor-pointer">
                        <span>{{ $latestAttempt ? 'Retake Final Exam' : 'Submit Final Exam' }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </button>
                </div>
            </form>
        @endif
    </div>
</x-dynamic-component>
