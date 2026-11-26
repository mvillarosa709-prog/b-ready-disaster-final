<x-layouts.admin>
    <x-slot name="title">Workshop Oversight — {{ $workshop->title }}</x-slot>

    <!-- Header Banner -->
    <div class="bg-white border-b border-slate-200/80">
        <div class="w-full px-4 py-7 sm:px-6 lg:px-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('admin.workshops.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-700 mb-2">
                    &larr; Back to Workshops Oversight
                </a>
                <div class="flex items-center gap-2.5">
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $workshop->title }}</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase {{ $workshop->status === 'published' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                        {{ $workshop->status }}
                    </span>
                </div>
                <p class="text-sm text-slate-500 mt-0.5">Trainer Facilitator: <strong>{{ $workshop->trainer?->name ?? 'Unassigned' }}</strong> &bull; Course: <strong>{{ $workshop->course?->title ?? 'None' }}</strong></p>
            </div>
        </div>
    </div>

    <div class="w-full px-4 sm:px-6 lg:px-8 mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        <div class="lg:col-span-2 space-y-6">
            <!-- Details Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
                <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Cohort Description & Curriculum</h2>
                <p class="text-xs text-slate-600 leading-relaxed">{{ $workshop->description }}</p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] uppercase font-bold text-slate-400">Start Date</span>
                        <p class="text-xs font-bold text-slate-800 mt-0.5">{{ $workshop->start_date ? $workshop->start_date->format('F d, Y') : 'TBD' }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] uppercase font-bold text-slate-400">End Date</span>
                        <p class="text-xs font-bold text-slate-800 mt-0.5">{{ $workshop->end_date ? $workshop->end_date->format('F d, Y') : 'TBD' }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] uppercase font-bold text-slate-400">Registration Deadline</span>
                        <p class="text-xs font-bold text-slate-800 mt-0.5">{{ $workshop->registration_deadline ? $workshop->registration_deadline->format('F d, Y') : 'TBD' }}</p>
                    </div>
                </div>
            </div>

            <!-- Enrolled Teachers Table -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Enrolled Teachers ({{ $workshop->teachers->count() }})</h2>
                        <p class="text-xs text-slate-500">Teachers actively attending or completed this training cohort.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/70 text-slate-500 uppercase tracking-wider font-bold text-[10px]">
                                <th class="py-3 px-4">Teacher</th>
                                <th class="py-3 px-4">Enrolled At</th>
                                <th class="py-3 px-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse ($workshop->teachers as $teacher)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-2.5">
                                            <span class="h-7 w-7 rounded-lg bg-sky-100 text-sky-800 font-bold flex items-center justify-center text-xs">
                                                {{ strtoupper(substr($teacher->name, 0, 1)) }}
                                            </span>
                                            <div>
                                                <p class="font-bold text-slate-900">{{ $teacher->name }}</p>
                                                <p class="text-[11px] text-slate-500">{{ $teacher->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-slate-500">
                                        {{ $teacher->pivot->joined_at ? \Carbon\Carbon::parse($teacher->pivot->joined_at)->format('M d, Y') : 'N/A' }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-100 text-emerald-800">
                                            {{ $teacher->pivot->status ?? 'Enrolled' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-6 text-center text-slate-400 text-xs">
                                        No teachers enrolled in this workshop yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <!-- Classroom Package Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2.5">Linked Classroom Package</h3>
                @if ($workshop->classroomPackage)
                    <div class="mt-3">
                        <p class="text-xs font-bold text-slate-900">{{ $workshop->classroomPackage->title }}</p>
                        <p class="text-[11px] text-slate-500 mt-1">{{ $workshop->classroomPackage->description }}</p>

                        <div class="mt-4 pt-3 border-t border-slate-100">
                            <span class="text-[10px] uppercase font-bold text-slate-400">Materials ({{ $workshop->classroomPackage->materials->count() }})</span>
                            <ul class="mt-2 space-y-1.5 text-xs text-slate-700">
                                @foreach ($workshop->classroomPackage->materials as $mat)
                                    <li class="flex items-center gap-2 p-1.5 rounded-lg bg-slate-50">
                                        <span class="text-indigo-600 font-bold">&bull;</span>
                                        <span class="truncate">{{ $mat->title }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @else
                    <p class="text-xs text-slate-400 mt-3">No classroom package generated yet.</p>
                @endif
            </div>
        </div>
    </div>
</x-layouts.admin>
