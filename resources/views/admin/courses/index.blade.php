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
                            <th class="py-3.5 px-4">Created Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse ($courses as $c)
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
                                <td class="py-3.5 px-4 text-[11px] text-slate-500">
                                    {{ $c->created_at ? $c->created_at->format('M d, Y') : 'System Default' }}
                                </td>
                            </tr>
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
