<x-dynamic-component component="layouts.teacher">
    <x-slot name="title">Classroom Packages</x-slot>
    <x-slot name="header">Classroom Packages</x-slot>

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

        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Workshop Packages &amp; Teaching Kits</h2>
                <p class="text-xs text-slate-500">Download packages materials, lesson guides, and classroom toolkits for your school.</p>
            </div>
            <a href="{{ route('teacher.implementations.create') }}" 
               class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-4 py-2.5 transition-colors shadow-2xs">
                + Log Classroom Implementation
            </a>
        </div>

        @if ($packages->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
                <div class="mx-auto h-12 w-12 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <h3 class="mt-3 text-sm font-bold text-slate-900">No classroom packages available</h3>
                <p class="mt-1 text-xs text-slate-500">
                    Register for DRR workshops to access their associated classroom package materials and lesson manuals.
                </p>
                <a href="{{ route('teacher.workshops.index') }}" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition-colors">
                    Browse classroom packages
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($packages as $pkg)
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col justify-between hover:shadow-md transition-all">
                        <div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-[11px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-100 rounded-md px-2.5 py-1">
                                    {{ optional($pkg->workshop)->title ?? 'Workshop Package' }}
                                </span>
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-extrabold text-emerald-700 border border-emerald-300 shadow-2xs">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    ✓ UNLOCKED
                                </span>
                            </div>

                            <h3 class="mt-3 text-base font-bold text-slate-900 line-clamp-1">
                                {{ $pkg->title }}
                            </h3>

                            <p class="mt-2 text-xs text-slate-600 line-clamp-3 leading-relaxed">
                                {{ $pkg->description ?? 'Comprehensive workshop repository featuring student manual, activity worksheets, assessment rubrics, and answer keys.' }}
                            </p>

                            <div class="mt-4 p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-600 flex items-center justify-between">
                                <span>Contained Materials:</span>
                                <span class="font-bold text-slate-800">{{ method_exists($pkg, 'materials') ? $pkg->materials->count() : 4 }} Learning Assets</span>
                            </div>
                        </div>

                        <div class="mt-5 pt-4 border-t border-slate-100 flex items-center gap-2">
                            <a href="{{ route('teacher.packages.show', $pkg) }}"
                               class="w-full text-center py-2 px-4 rounded-xl text-xs font-bold transition-colors bg-indigo-600 hover:bg-indigo-700 text-white shadow-2xs">
                                Access Repository &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-dynamic-component>