<x-layouts.admin>
    <x-slot name="title">Permissions & Access Matrix</x-slot>

    <!-- Header Banner -->
    <div class="bg-white border-b border-slate-200/80">
        <div class="w-full px-4 py-7 sm:px-6 lg:px-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        SECURITY & ACCESS GOVERNANCE
                    </span>
                    <span class="text-xs text-slate-500">• {{ count($permissions) }} Configurable Capabilities</span>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 mt-1 tracking-tight">Permissions & Module Access Matrix</h1>
                <p class="text-sm text-slate-500 mt-1">Review default capabilities by system role and inspect individual user permission overrides.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.users.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm shadow-emerald-600/30 transition">
                    Add User with Custom Access
                </a>
            </div>
        </div>
    </div>

    <div class="w-full px-4 sm:px-6 lg:px-8 mt-6 space-y-6">
        <!-- Matrix Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900">Standard Role Capability Matrix</h2>
                <p class="text-xs text-slate-500">Default access level assigned to each account type upon creation.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/70 text-slate-600 uppercase tracking-wider font-bold text-[10px]">
                            <th class="py-3.5 px-4">Module / Capability</th>
                            <th class="py-3.5 px-4">System Key</th>
                            <th class="py-3.5 px-4 text-center">
                                <span class="px-2 py-0.5 rounded bg-slate-900 text-emerald-400 font-bold">Super Admin</span>
                            </th>
                            <th class="py-3.5 px-4 text-center">
                                <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold">DRR Trainer</span>
                            </th>
                            <th class="py-3.5 px-4 text-center">
                                <span class="px-2 py-0.5 rounded bg-sky-100 text-sky-800 font-bold">Teacher</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach ($permissions as $key => $perm)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4">
                                    <p class="font-bold text-slate-900">{{ $perm['label'] }}</p>
                                    <p class="text-[11px] text-slate-500 mt-0.5">{{ $perm['description'] }}</p>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-[11px] text-slate-600">
                                    {{ $key }}
                                </td>
                                <!-- Super Admin Column -->
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-emerald-100 text-emerald-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </span>
                                </td>
                                <!-- Trainer Column -->
                                <td class="py-3.5 px-4 text-center">
                                    @if (in_array($key, $trainerDefaults))
                                        <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-emerald-100 text-emerald-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-slate-100 text-slate-400">
                                            &mdash;
                                        </span>
                                    @endif
                                </td>
                                <!-- Teacher Column -->
                                <td class="py-3.5 px-4 text-center">
                                    @if (in_array($key, $teacherDefaults))
                                        <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-emerald-100 text-emerald-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-slate-100 text-slate-400">
                                            &mdash;
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Custom User Overrides Section -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Customized User Permission Overrides</h2>
                    <p class="text-xs text-slate-500">Users who have been granted explicit permissions deviating from their standard role defaults.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/70 text-slate-500 uppercase tracking-wider font-bold text-[10px]">
                            <th class="py-3 px-4">User</th>
                            <th class="py-3 px-4">Role</th>
                            <th class="py-3 px-4">Assigned Custom Permissions</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse ($customizedUsers as $u)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4">
                                    <p class="font-bold text-slate-900">{{ $u->name }}</p>
                                    <p class="text-[11px] text-slate-500">{{ $u->email }}</p>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $u->role === 'trainer' ? 'bg-emerald-100 text-emerald-800' : 'bg-sky-100 text-sky-800' }}">
                                        {{ $u->role }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="flex flex-wrap gap-1 max-w-md">
                                        @foreach ($u->permissions ?? [] as $permKey)
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                {{ $permissions[$permKey]['label'] ?? $permKey }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <a href="{{ route('admin.users.edit', $u) }}" class="px-3 py-1 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-[11px] shadow-2xs transition">
                                        Modify
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400 text-xs">
                                    All users are currently operating under standard role permissions. No overrides detected.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>
