<x-dynamic-component component="layouts.teacher">
    <x-slot name="title">Workshop Packages — B-READY</x-slot>

    <!-- Header Banner (Matches Trainer Side) -->
    <div class="bg-white border-b border-slate-200/80 px-4 py-8 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        Classroom Resources
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2 tracking-tight">
                    Workshop Packages &amp; Teaching Kits
                </h1>
                <p class="text-slate-500 text-sm mt-1">
                    Once you earn your badge and approved certificate, unlock teaching toolkits here and start your classroom implementation.
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
                    + Log Implementation
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 space-y-6">
        @if ($packages->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center shadow-xs">
                <h3 class="text-base font-bold text-slate-900">No classroom packages unlocked yet</h3>
                <p class="mt-1 text-xs text-slate-500 max-w-md mx-auto">
                    Enroll in a DRR workshop, complete all modules, and earn your badge &amp; approved certificate. Your unlocked workshop teaching kits will appear here so you can start classroom implementation.
                </p>
                <a href="{{ route('teacher.workshops.index') }}" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-xs font-semibold text-white hover:bg-emerald-700 shadow-xs transition">
                    Find Workshops
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($packages as $pkg)
                    @php
                        $isCertified = in_array($pkg->workshop_id, $certifiedWorkshopIds ?? []);
                        $cert = isset($certificationsByWorkshop) ? $certificationsByWorkshop->get($pkg->workshop_id) : null;
                    @endphp
                    <div class="bg-white rounded-2xl border {{ $isCertified ? 'border-emerald-200/80' : 'border-slate-200/80' }} p-6 shadow-xs flex flex-col justify-between hover:shadow-md transition-all">
                        <div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200/70 rounded-md px-2.5 py-0.5 truncate max-w-[60%]">
                                    {{ optional($pkg->workshop)->title ?? 'Workshop Package' }}
                                </span>
                                @if ($isCertified)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800 shrink-0">
                                        ✓ Unlocked
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-800 shrink-0">
                                        Locked
                                    </span>
                                @endif
                            </div>

                            <h3 class="mt-3 text-base font-bold text-slate-900 line-clamp-1">
                                {{ $pkg->title }}
                            </h3>

                            <p class="mt-2 text-xs text-slate-600 line-clamp-2 leading-relaxed">
                                {{ $pkg->description ?? 'Comprehensive workshop repository featuring student manual, activity worksheets, assessment rubrics, and answer keys.' }}
                            </p>

                            @if ($isCertified && $cert)
                                <div class="mt-4 p-3 rounded-xl bg-emerald-50/70 border border-emerald-200/70 text-xs space-y-1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-emerald-700 font-medium">Earned Badge:</span>
                                        <span class="font-bold text-emerald-900">{{ $cert->badge_name ?? 'DRR Certified Educator' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-emerald-700 font-medium">Certificate No:</span>
                                        <span class="font-mono font-bold text-emerald-900">{{ $cert->certificate_number }}</span>
                                    </div>
                                </div>
                            @else
                                <div class="mt-4 p-3 rounded-xl bg-amber-50/60 border border-amber-200/60 text-xs text-amber-800">
                                    Complete workshop modules and earn your approved certificate &amp; badge to unlock and start using this package.
                                </div>
                            @endif

                            <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-600 flex items-center justify-between">
                                <span>Contained Materials:</span>
                                <span class="font-bold text-slate-800">{{ method_exists($pkg, 'materials') ? $pkg->materials->count() : 0 }} Learning Assets</span>
                            </div>
                        </div>

                        <div class="mt-5 pt-4 border-t border-slate-100 space-y-2">
                            @if ($isCertified)
                                <a href="{{ route('teacher.implementations.create') }}"
                                   class="block w-full text-center py-2.5 px-4 rounded-xl text-xs font-semibold transition-colors bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs">
                                    Start Classroom Implementation &rarr;
                                </a>
                                <a href="{{ route('teacher.packages.show', $pkg) }}"
                                   class="block w-full text-center py-2 px-4 rounded-xl text-xs font-semibold transition-colors border border-slate-300 bg-white hover:bg-slate-50 text-slate-700">
                                    Access Repository Materials
                                </a>
                            @else
                                <a href="{{ route('teacher.workshops.show', $pkg->workshop_id) }}"
                                   class="block w-full text-center py-2.5 px-4 rounded-xl text-xs font-semibold transition-colors bg-slate-800 hover:bg-slate-900 text-white shadow-xs">
                                    Complete Workshop to Unlock &rarr;
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-dynamic-component>
