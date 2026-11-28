<x-layouts.admin>
    <x-slot name="title">Courses Oversight — Super Admin</x-slot>

    <!-- Header Banner -->
    <div class="bg-white border-b border-slate-200/80">
        <div class="w-full px-4 py-7 sm:px-6 lg:px-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                        CURRICULUM GOVERNANCE
                    </span>
                    <span class="text-xs text-slate-500">• {{ $courses->total() }} Master Courses</span>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 mt-1 tracking-tight">Courses & Modules Oversight</h1>
                <p class="text-sm text-slate-500 mt-1">Review DRR master curricula, sequence of modules, downloadable materials, and quizzes.</p>
            </div>
        </div>
    </div>

    <div class="w-full px-4 sm:px-6 lg:px-8 mt-6">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/70 text-slate-500 uppercase tracking-wider font-bold text-[10px]">
                            <th class="py-3.5 px-4">Course Details</th>
                            <th class="py-3.5 px-4">Author Trainer</th>
                            <th class="py-3.5 px-4">Modules Count</th>
                            <th class="py-3.5 px-4">Active Workshops</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-right">Curriculum &amp; Materials</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse ($courses as $c)
                            <tbody x-data="{ open: false }" class="divide-y divide-slate-100">
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="py-3.5 px-4 max-w-md">
                                        <p class="font-bold text-slate-900">{{ $c->title }}</p>
                                        <p class="text-[11px] text-slate-500 line-clamp-1 mt-0.5">{{ $c->description }}</p>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="font-semibold text-slate-800">{{ $c->trainer?->name ?? 'Unassigned' }}</span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex items-center gap-1 font-bold text-slate-800 bg-slate-100 px-2.5 py-1 rounded-lg">
                                            {{ $c->modules_count }} modules
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="font-semibold text-slate-700">{{ $c->workshops_count }} linked</span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $c->status === 'published' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $c->status }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <button type="button" @click="open = !open" 
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-[11px] shadow-2xs transition">
                                            <span x-text="open ? 'Close Materials' : 'Inspect Materials'"></span>
                                            <svg class="w-3.5 h-3.5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                <tr x-show="open" style="display: none;" class="bg-slate-50/70 border-b border-slate-200/80">
                                    <td colspan="6" class="p-5">
                                        <div class="bg-white rounded-xl border border-slate-200 p-5 space-y-4 shadow-xs">
                                            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                                <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                                    Curriculum Modules &amp; Uploaded Training Materials
                                                </h4>
                                                <span class="text-[11px] text-slate-500">{{ $c->modules->count() }} Learning Units</span>
                                            </div>

                                            @if ($c->modules->isEmpty())
                                                <p class="text-xs text-slate-400 italic">No modules attached to this master course yet.</p>
                                            @else
                                                <div class="space-y-3">
                                                    @foreach ($c->modules as $mod)
                                                        <div class="p-3.5 rounded-lg border border-slate-100 bg-slate-50/50 space-y-2">
                                                            <div class="flex flex-wrap items-center justify-between gap-2">
                                                                <div class="flex items-center gap-2">
                                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-700">Module {{ $mod->sequence }}</span>
                                                                    <span class="font-bold text-slate-900 text-xs">{{ $mod->title }}</span>
                                                                </div>
                                                                <span class="text-[11px] text-slate-500">{{ $mod->materials->count() }} Training Materials</span>
                                                            </div>

                                                            @if ($mod->materials->isEmpty())
                                                                <p class="text-[11px] text-slate-400 pl-4">No materials uploaded for this module.</p>
                                                            @else
                                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 pt-1 pl-2">
                                                                    @foreach ($mod->materials as $mat)
                                                                        <div class="flex items-center justify-between p-2 rounded-md bg-white border border-slate-200 text-xs">
                                                                            <div class="flex items-center gap-2 min-w-0 pr-2">
                                                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase shrink-0 
                                                                                    {{ $mat->type === 'presentation' ? 'bg-amber-100 text-amber-800' : ($mat->type === 'video' ? 'bg-sky-100 text-sky-800' : 'bg-emerald-100 text-emerald-800') }}">
                                                                                    {{ $mat->type }}
                                                                                </span>
                                                                                <span class="font-semibold text-slate-800 truncate" title="{{ $mat->title }}">{{ $mat->title }}</span>
                                                                            </div>
                                                                            @if ($mat->file_path)
                                                                                <a href="{{ asset('storage/' . $mat->file_path) }}" target="_blank" download
                                                                                   class="shrink-0 text-[11px] font-bold text-emerald-600 hover:text-emerald-700 hover:underline">
                                                                                    Download &darr;
                                                                                </a>
                                                                            @elseif ($mat->external_url)
                                                                                <a href="{{ $mat->external_url }}" target="_blank"
                                                                                   class="shrink-0 text-[11px] font-bold text-sky-600 hover:text-sky-700 hover:underline">
                                                                                    Visit Link &rarr;
                                                                                </a>
                                                                            @endif
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400 text-xs">
                                    No courses currently authored in the system.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($courses->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $courses->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
