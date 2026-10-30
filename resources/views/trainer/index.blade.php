<x-layouts.trainer>
    <x-slot name="title">Assessment Questions Bank — B-READY</x-slot>

    <!-- Header Banner -->
    <div class="bg-white border-b border-slate-200/80 px-4 py-8 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        Assessment & Question Bank
                    </span>
                    <span class="text-xs text-slate-400">Automated Scoring System</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2 tracking-tight">
                    Assessment Questions & Items
                </h1>
                <p class="text-slate-500 text-sm mt-1">
                    Manage quiz items, multiple choice answers, and upload bulk questions via CSV.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('trainer.assessments.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Upload CSV Questions
                </a>
                <a href="{{ route('trainer.assessments.take') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50 shadow-xs transition">
                    Simulate Exam
                </a>
            </div>
        </div>
    </div>

    <!-- Questions Bank Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        @if($questions->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center shadow-xs">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-slate-100 text-slate-400 mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <p class="text-base font-bold text-slate-800">No assessment questions in the question bank yet.</p>
                <p class="text-sm text-slate-500 mt-1">Upload a CSV file containing disaster risk reduction questions and multiple choices.</p>
                <div class="mt-5">
                    <a href="{{ route('trainer.assessments.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 shadow-sm transition">
                        Upload CSV Now
                    </a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 gap-4">
                @foreach($questions as $index => $q)
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs hover:border-emerald-200 transition">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-start gap-3">
                                <span class="h-7 w-7 rounded-lg bg-emerald-50 text-emerald-700 font-bold text-xs flex items-center justify-center shrink-0 border border-emerald-100">
                                    Q{{ $index + 1 }}
                                </span>
                                <div>
                                    <h3 class="font-bold text-slate-900 text-base leading-snug">
                                        {{ $q->question_text }}
                                    </h3>
                                    <span class="text-xs text-slate-400 mt-0.5 block">Multiple Choice • Sequence #{{ $q->sequence ?? ($index + 1) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Choices List -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 mt-5 pl-10">
                            @foreach($q->choices as $cIdx => $choice)
                                <div class="p-3 rounded-xl border {{ $choice->is_correct ? 'border-emerald-300 bg-emerald-50/60 font-semibold text-emerald-900' : 'border-slate-200 bg-slate-50/50 text-slate-600' }} flex items-center justify-between text-sm">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-5 h-5 rounded-full {{ $choice->is_correct ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-600' }} text-[10px] font-bold flex items-center justify-center">
                                            {{ chr(65 + $cIdx) }}
                                        </span>
                                        <span>{{ $choice->choice_text }}</span>
                                    </div>
                                    @if($choice->is_correct)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-200 text-emerald-800">
                                            Correct Answer
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.trainer>