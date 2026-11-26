<x-layouts.admin>
    <x-slot name="title">Workshops Oversight — Super Admin</x-slot>

    <!-- Header Banner -->
    <div class="bg-white border-b border-slate-200/80">
        <div class="w-full px-4 py-7 sm:px-6 lg:px-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                        WORKSHOPS OVERSIGHT
                    </span>
                    <span class="text-xs text-slate-500">• {{ $workshops->total() }} Total Cohorts</span>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 mt-1 tracking-tight">Platform Workshops Governance</h1>
                <p class="text-sm text-slate-500 mt-1">Audit training cohorts, enrolled teachers, and linked classroom packages across all trainers.</p>
            </div>
        </div>
    </div>

    <div class="w-full px-4 sm:px-6 lg:px-8 mt-6">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/70 text-slate-500 uppercase tracking-wider font-bold text-[10px]">
                            <th class="py-3.5 px-4">Workshop Title</th>
                            <th class="py-3.5 px-4">Facilitating Trainer</th>
                            <th class="py-3.5 px-4">Enrolled Teachers</th>
                            <th class="py-3.5 px-4">Timeline</th>
                            <th class="py-3.5 px-4">Classroom Kit</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse ($workshops as $ws)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4 max-w-xs">
                                    <p class="font-bold text-slate-900 line-clamp-1">{{ $ws->title }}</p>
                                    <p class="text-[11px] text-slate-500 line-clamp-1 mt-0.5">{{ $ws->course?->title ?? 'No course linked' }}</p>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-2">
                                        <span class="h-6 w-6 rounded-lg bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-[10px]">
                                            {{ strtoupper(substr($ws->trainer?->name ?? 'T', 0, 1)) }}
                                        </span>
                                        <span class="font-semibold text-slate-800">{{ $ws->trainer?->name ?? 'Unassigned' }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center gap-1 font-bold text-slate-800 bg-slate-100 px-2.5 py-1 rounded-lg">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        {{ $ws->teachers_count }} teachers
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-[11px] text-slate-500">
                                    {{ $ws->start_date ? $ws->start_date->format('M d') : 'TBD' }} &mdash; {{ $ws->end_date ? $ws->end_date->format('M d, Y') : 'TBD' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @if ($ws->classroomPackage)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                            Kit Ready
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-[11px]">No package</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $ws->status === 'published' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $ws->status }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <a href="{{ route('admin.workshops.show', $ws) }}" class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs shadow-2xs transition">
                                        Inspect
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400 text-xs">
                                    No workshops recorded in the platform yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($workshops->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $workshops->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
