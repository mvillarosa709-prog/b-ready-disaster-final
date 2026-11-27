<x-layouts.admin>
    <x-slot name="title">Super Admin Dashboard</x-slot>

    <!-- Header Banner (Matches Teacher & Trainer Banner) -->
    <div class="bg-white border-b border-slate-200/80">
        <div class="w-full px-4 py-7 sm:px-6 lg:px-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        SUPER ADMIN CONTROL CONSOLE
                    </span>
                    <span class="text-xs text-slate-500">• Unified Access Management</span>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 mt-1 tracking-tight">System Administration Overview</h1>
                <p class="text-sm text-slate-500 mt-1">Configure user accounts, manage system roles, and monitor DRR training delivery across schools.</p>
            </div>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="w-full px-4 sm:px-6 lg:px-8 mt-6 space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Total Users Card -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-emerald-200 transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">System Users</span>
                    <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-black text-slate-900">{{ $totalUsers }}</span>
                    <span class="text-xs text-slate-400">total accounts</span>
                </div>
                <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                    <span>Admin: <strong class="text-slate-800">{{ $adminCount }}</strong></span>
                    <span>Trainer: <strong class="text-emerald-700">{{ $trainerCount }}</strong></span>
                    <span>Teacher: <strong class="text-sky-700">{{ $teacherCount }}</strong></span>
                </div>
            </div>

            <!-- Workshops Card -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-emerald-200 transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Workshops</span>
                    <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-black text-slate-900">{{ $totalWorkshops }}</span>
                    <span class="text-xs text-slate-400">cohorts</span>
                </div>
                <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                    <span>Published: <strong class="text-emerald-600">{{ $publishedWorkshops }}</strong></span>
                    <a href="{{ route('admin.workshops.index') }}" class="font-bold text-emerald-600 hover:underline">View all &rarr;</a>
                </div>
            </div>

            <!-- Courses & Curriculum Card -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-emerald-200 transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Courses Curriculum</span>
                    <span class="p-2 rounded-xl bg-amber-50 text-amber-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-black text-slate-900">{{ $totalCourses }}</span>
                    <span class="text-xs text-slate-400">master courses</span>
                </div>
                <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                    <span>Active: <strong class="text-amber-700">{{ $publishedCourses }} published</strong></span>
                    <a href="{{ route('admin.courses.index') }}" class="font-bold text-emerald-600 hover:underline">Inspect &rarr;</a>
                </div>
            </div>

            <!-- Certifications Card (Auto-Generated upon Passing) -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-emerald-200 transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Certifications</span>
                    <span class="p-2 rounded-xl bg-sky-50 text-sky-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-black text-slate-900">{{ $approvedCertificates }}</span>
                    <span class="text-xs text-slate-400">issued certificates</span>
                </div>
                <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                    <span class="flex items-center gap-1.5 text-emerald-700 font-semibold">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Auto-Generated on Pass
                    </span>
                    <span class="text-slate-400">Instant</span>
                </div>
            </div>
        </div>

        <!-- Recent Users & System Overview Table -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            <!-- Recent Users Section -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">User Directory & Role Assignments</h3>
                        <p class="text-xs text-slate-500">Recently registered and provisioned platform accounts.</p>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">
                        View All Users &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/70 text-slate-500 uppercase tracking-wider font-bold text-[10px]">
                                <th class="py-3 px-4">User</th>
                                <th class="py-3 px-4">Role</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @foreach ($recentUsers as $user)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-2.5">
                                             <span class="h-7 w-7 rounded-lg bg-emerald-50 text-emerald-700 font-bold flex items-center justify-center text-[11px] shrink-0 border border-emerald-200">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </span>
                                            <div class="min-w-0">
                                                <p class="font-bold text-slate-900 truncate">{{ $user->name }}</p>
                                                <p class="text-[11px] text-slate-500 truncate">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        @if ($user->role === 'admin')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-slate-900 text-emerald-400">
                                                Super Admin
                                            </span>
                                        @elseif ($user->role === 'trainer')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                                Trainer
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-sky-100 text-sky-800">
                                                Teacher
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        @if ($user->is_active ?? true)
                                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-rose-600">
                                                <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span> Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <a href="{{ route('admin.users.edit', $user) }}" 
                                           class="inline-flex items-center px-2.5 py-1 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-[11px] shadow-2xs transition">
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Side Card: Active Workshops Snapshot -->
            <div class="space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="text-sm font-bold text-slate-900">Active Workshops</h3>
                        <a href="{{ route('admin.workshops.index') }}" class="text-[11px] font-bold text-emerald-600 hover:underline">
                            All cohorts &rarr;
                        </a>
                    </div>
                    <div class="mt-3 divide-y divide-slate-100">
                        @foreach ($recentWorkshops as $ws)
                            <div class="py-2.5">
                                <p class="text-xs font-bold text-slate-900 line-clamp-1">{{ $ws->title }}</p>
                                <div class="flex items-center justify-between mt-1 text-[11px] text-slate-500">
                                    <span>Trainer: {{ $ws->trainer?->name ?? 'Unassigned' }}</span>
                                    <span class="px-2 py-0.5 rounded font-semibold {{ $ws->status === 'published' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ ucfirst($ws->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
