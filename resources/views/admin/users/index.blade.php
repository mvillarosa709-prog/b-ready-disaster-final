<x-layouts.admin>
    <x-slot name="title">User & Role Management</x-slot>

    <!-- Header Banner -->
    <div class="bg-white border-b border-slate-200/80">
        <div class="w-full px-4 py-7 sm:px-6 lg:px-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        ACCESS CONTROL & ROLES
                    </span>
                    <span class="text-xs text-slate-500">• Manage {{ $stats['total'] }} Accounts</span>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 mt-1 tracking-tight">System User Directory</h1>
                <p class="text-sm text-slate-500 mt-1">Manage Teacher and Trainer accounts and send login credentials directly to their Gmail.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.users.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm shadow-emerald-600/30 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    <span>Create New User</span>
                </a>
            </div>
        </div>
    </div>

    <div class="w-full px-4 sm:px-6 lg:px-8 mt-6 space-y-6">
        <!-- Stats Pill Counters -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <a href="{{ route('admin.users.index') }}" class="p-3.5 rounded-xl border {{ !request('role') && !request('status') ? 'bg-emerald-50/70 border-emerald-300 ring-1 ring-emerald-300' : 'bg-white border-slate-200/80 hover:bg-slate-50' }} transition">
                <span class="text-[11px] font-bold text-slate-500 uppercase">All Users</span>
                <p class="text-xl font-black text-slate-900 mt-1">{{ $stats['total'] }}</p>
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'admin']) }}" class="p-3.5 rounded-xl border {{ request('role') === 'admin' ? 'bg-slate-900 border-slate-900 text-white' : 'bg-white border-slate-200/80 hover:bg-slate-50' }} transition">
                <span class="text-[11px] font-bold {{ request('role') === 'admin' ? 'text-emerald-400' : 'text-slate-700' }} uppercase">Super Admins</span>
                <p class="text-xl font-black {{ request('role') === 'admin' ? 'text-white' : 'text-slate-900' }} mt-1">{{ $stats['admins'] }}</p>
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'trainer']) }}" class="p-3.5 rounded-xl border {{ request('role') === 'trainer' ? 'bg-emerald-50/70 border-emerald-300 ring-1 ring-emerald-300' : 'bg-white border-slate-200/80 hover:bg-slate-50' }} transition">
                <span class="text-[11px] font-bold text-emerald-700 uppercase">Trainers</span>
                <p class="text-xl font-black text-emerald-900 mt-1">{{ $stats['trainers'] }}</p>
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'teacher']) }}" class="p-3.5 rounded-xl border {{ request('role') === 'teacher' ? 'bg-sky-50/70 border-sky-300 ring-1 ring-sky-300' : 'bg-white border-slate-200/80 hover:bg-slate-50' }} transition">
                <span class="text-[11px] font-bold text-sky-700 uppercase">Teachers</span>
                <p class="text-xl font-black text-sky-900 mt-1">{{ $stats['teachers'] }}</p>
            </a>
            <a href="{{ route('admin.users.index', ['status' => 'active']) }}" class="p-3.5 rounded-xl border {{ request('status') === 'active' ? 'bg-emerald-50/70 border-emerald-300 ring-1 ring-emerald-300' : 'bg-white border-slate-200/80 hover:bg-slate-50' }} transition">
                <span class="text-[11px] font-bold text-slate-500 uppercase">Active</span>
                <p class="text-xl font-black text-emerald-600 mt-1">{{ $stats['active'] }}</p>
            </a>
            <a href="{{ route('admin.users.index', ['status' => 'inactive']) }}" class="p-3.5 rounded-xl border {{ request('status') === 'inactive' ? 'bg-rose-50/70 border-rose-300 ring-1 ring-rose-300' : 'bg-white border-slate-200/80 hover:bg-slate-50' }} transition">
                <span class="text-[11px] font-bold text-slate-500 uppercase">Deactivated</span>
                <p class="text-xl font-black text-rose-600 mt-1">{{ $stats['inactive'] }}</p>
            </a>
        </div>

        <!-- Filters & Search Toolbar -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-1 flex-wrap items-center gap-3">
                <div class="relative flex-1 min-w-[220px]">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search by user name or email address..." 
                           class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border-slate-200 bg-slate-50/70 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500/20">
                </div>

                <select name="role" class="text-xs rounded-xl border-slate-200 bg-slate-50/70 py-2 px-3 focus:border-emerald-500 focus:ring-emerald-500/20">
                    <option value="">All Roles</option>
                    <option value="admin" @selected(request('role') === 'admin')>Super Admin</option>
                    <option value="trainer" @selected(request('role') === 'trainer')>DRR Trainer</option>
                    <option value="teacher" @selected(request('role') === 'teacher')>Teacher</option>
                </select>

                <select name="status" class="text-xs rounded-xl border-slate-200 bg-slate-50/70 py-2 px-3 focus:border-emerald-500 focus:ring-emerald-500/20">
                    <option value="">All Statuses</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Deactivated</option>
                </select>

                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold transition">
                    Filter
                </button>

                @if(request()->anyFilled(['search', 'role', 'status']))
                    <a href="{{ route('admin.users.index') }}" class="px-3 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 text-xs font-semibold transition">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/70 text-slate-500 uppercase tracking-wider font-bold text-[10px]">
                            <th class="py-3.5 px-4">User</th>
                            <th class="py-3.5 px-4">Role</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4">Joined Date</th>
                            <th class="py-3.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse ($users as $user)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <span class="h-8 w-8 rounded-xl font-bold flex items-center justify-center text-xs shrink-0 
                                            {{ $user->role === 'admin' ? 'bg-slate-900 text-emerald-400' : ($user->role === 'trainer' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-sky-100 text-sky-700 border border-sky-200') }}">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </span>
                                        <div class="min-w-0">
                                            <p class="font-bold text-slate-900 truncate">{{ $user->name }}</p>
                                            <p class="text-[11px] text-slate-500 truncate">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    @if ($user->role === 'admin')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-900 text-emerald-400">
                                            Super Admin
                                        </span>
                                    @elseif ($user->role === 'trainer')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            DRR Trainer
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-sky-100 text-sky-800 border border-sky-200">
                                            Teacher
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    @if ($user->is_active ?? true)
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[11px] font-semibold text-rose-700 bg-rose-50 border border-rose-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span> Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-slate-500 text-[11px]">
                                    {{ $user->created_at ? $user->created_at->format('M d, Y') : 'System Default' }}
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('admin.users.edit', $user) }}" 
                                           class="px-2.5 py-1 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-[11px] shadow-2xs transition">
                                            Edit
                                        </a>

                                        @if ($user->id !== auth()->id() && $user->email !== 'adminbready@gmail.com')
                                            <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="px-2.5 py-1 rounded-lg border {{ $user->is_active ? 'border-amber-200 text-amber-700 hover:bg-amber-50' : 'border-emerald-200 text-emerald-700 hover:bg-emerald-50' }} font-semibold text-[11px] transition">
                                                    {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete user \'{{ $user->name }}\'? This action cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2.5 py-1 rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50 font-semibold text-[11px] transition">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400 text-xs">
                                    No users found matching your criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
