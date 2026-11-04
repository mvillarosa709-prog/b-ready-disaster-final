<x-dynamic-component component="layouts.teacher">
    <x-slot name="title">{{ $package->title }} &bull; Workshop Repository</x-slot>
    <x-slot name="header">Workshop Repository</x-slot>

    <div class="space-y-6">
        <!-- Top Back Bar -->
        <div class="flex items-center justify-between">
            <a href="{{ route('teacher.packages.index') }}" 
               class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-indigo-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Workshop Repository
            </a>

            <span class="text-xs font-semibold text-slate-500">
                Workshop: <strong class="text-slate-800">{{ $package->workshop->title }}</strong>
            </span>
        </div>

        <!-- Repository Overview Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="max-w-3xl">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-sky-700 bg-sky-50 border border-sky-100 rounded-md px-2.5 py-1">
                            Workshop Repository
                        </span>
                        @if ($isUnlocked)
                            <span class="inline-flex items-center gap-1 text-xs font-extrabold text-emerald-700 bg-emerald-50 border border-emerald-300 px-3 py-1 rounded-full shadow-2xs">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                ✓ UNLOCKED
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs font-extrabold text-slate-500 bg-slate-100 border border-slate-200 px-3 py-1 rounded-full">
                                🔒 LOCKED
                            </span>
                        @endif
                    </div>

                    <h2 class="mt-3 text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        {{ $package->title }}
                    </h2>

                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                        {{ $package->description ?? 'Official workshop repository containing comprehensive lesson plans, student manuals, emergency drill sheets, and assessment answer keys.' }}
                    </p>

                    <div class="mt-4 text-xs text-slate-500">
                        Designed by Trainer: <span class="font-bold text-slate-800">{{ $package->trainer->name ?? 'DRR Specialist' }}</span>
                    </div>
                </div>

                <!-- Right Action Block -->
                <div class="lg:w-80 shrink-0 p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Classroom Rollout</span>
                    @if ($isUnlocked)
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Now that you hold accreditation for this workshop, execute your classroom teaching session and log student performance metrics.
                        </p>
                        <a href="{{ route('teacher.implementations.create', ['workshop_id' => $package->workshop_id]) }}"
                           class="block text-center w-full py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs transition-colors">
                            Log Classroom Implementation &rarr;
                        </a>
                    @else
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Pass the workshop final assessment to unlock all materials and enable implementation reporting.
                        </p>
                        <a href="{{ route('teacher.workshops.show', $package->workshop) }}"
                           class="block text-center w-full py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs transition-colors">
                            Go to Workshop Modules &rarr;
                        </a>
                    @endif
                </div>
            </div>
        </div>

        @if (! $isUnlocked)
            <!-- Locked Gating Banner -->
            <div class="rounded-2xl border border-amber-200 bg-amber-50/70 p-6 sm:p-8 text-amber-900 shadow-xs space-y-4">
                <div class="flex items-start gap-4">
                    <div class="p-3 rounded-2xl bg-amber-100 text-amber-800 shrink-0">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Workshop Repository Materials are Gated</h3>
                        <p class="text-xs sm:text-sm text-amber-800 mt-1 leading-relaxed">
                            To ensure educational fidelity and disaster safety compliance, workshop repository materials and student manuals are unlocked only after you successfully complete all required modules and achieve a passing score on the final assessment.
                        </p>
                    </div>
                </div>

                <div class="pt-2 flex flex-wrap gap-3">
                    <a href="{{ route('teacher.workshops.show', $package->workshop) }}" 
                       class="inline-flex items-center gap-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold px-4 py-2.5 shadow-2xs transition-colors">
                        Go to Workshop Modules
                    </a>
                </div>
            </div>
        @endif

        <!-- Materials List -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Included Repository Assets</h3>
                    <p class="text-xs text-slate-500">Student manuals, facilitator outlines, emergency drills, and grading keys.</p>
                </div>
                <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">
                    {{ $package->materials->count() }} Assets
                </span>
            </div>

            @if ($package->materials->isEmpty())
                <div class="p-8 text-center text-xs text-slate-400 border border-dashed border-slate-200 rounded-xl">
                    No repository materials currently registered.
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($package->materials as $mat)
                        @php
                            $label = str_replace('_', ' ', $mat->material_type);
                        @endphp

                        <div class="p-4 rounded-xl border border-slate-200/80 bg-slate-50/50 flex flex-col justify-between hover:border-indigo-200 transition-all {{ ! $isUnlocked ? 'opacity-60 cursor-not-allowed' : '' }}">
                            <div>
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-700 bg-indigo-50 border border-indigo-100 rounded px-2 py-0.5">
                                        {{ $label }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-mono">
                                        Seq #{{ $mat->sequence }}
                                    </span>
                                </div>

                                <h4 class="mt-2 text-sm font-bold text-slate-900">
                                    {{ $mat->title }}
                                </h4>

                                @if ($mat->description)
                                    <p class="mt-1 text-xs text-slate-500 line-clamp-2">{{ $mat->description }}</p>
                                @endif
                            </div>

                            <div class="mt-4 pt-3 border-t border-slate-200/60 flex items-center justify-between">
                                <span class="text-[11px] text-slate-400">
                                    {{ $mat->original_filename ?? 'Downloadable PDF / Guide' }}
                                </span>

                                @if ($isUnlocked)
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('teacher.packages.material.preview', [$package, $mat]) }}" target="_blank"
                                           class="inline-flex items-center gap-1 text-xs font-semibold text-slate-700 hover:text-indigo-600 bg-white hover:bg-slate-50 px-2.5 py-1.5 rounded-lg border border-slate-200 transition-colors shadow-2xs">
                                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Preview
                                        </a>
                                        <a href="{{ route('teacher.packages.material.download', [$package, $mat]) }}"
                                           class="inline-flex items-center gap-1.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-1.5 rounded-lg shadow-2xs transition-colors">
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                            Download
                                        </a>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 flex items-center gap-1 font-bold">
                                        🔒 LOCKED
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-dynamic-component>
