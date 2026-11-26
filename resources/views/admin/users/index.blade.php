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
                <p class="text-sm text-slate-500 mt-1">Gumawa ng accounts para sa Teacher at Trainer, at ipadala ang credentials via Gmail / Brevo.</p>
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
        <!-- Permanent Portal Login Link Bar -->
        <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-emerald-50 via-teal-50 to-slate-50 border border-emerald-200/90 shadow-2xs flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-start sm:items-center gap-3.5 min-w-0">
                <div class="p-2.5 rounded-xl bg-emerald-600 text-white shrink-0 shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Teacher &amp; Trainer Portal Login Link</h2>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            Direct Link
                        </span>
                    </div>
                    <p class="text-xs text-slate-600 mt-1 font-mono select-all truncate">
                        {{ route('login') }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                <button type="button" 
                        onclick="copyTextToClipboard('{{ route('login') }}', this, 'Link Na-kopya!')"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-slate-300 hover:border-emerald-500 text-slate-700 hover:text-emerald-700 text-xs font-bold shadow-2xs transition">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                    </svg>
                    <span>Kopyahin ang Login Link</span>
                </button>
                <a href="{{ route('admin.users.create') }}" 
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm shadow-emerald-600/30 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    <span>+ Mag-provision ng Bagong User</span>
                </a>
            </div>
        </div>

        @if (session('new_user_provisioned'))
            @php 
                $newU = session('new_user_provisioned'); 
                $mailBody = "Magandang araw {$newU['name']}!\n\nNagawa na ang iyong opisyal na B-READY account bilang " . ucfirst($newU['role']) . ".\n\nNarito ang iyong login details:\n• Email: {$newU['email']}\n• Temporary Password: {$newU['password']}\n• Portal Login Link: {$newU['login_url']}\n\nPaalala: Sa unang pag-log in, hihilingan ka ng system na magtakda ng iyong bagong personal password bago ma-access ang dashboard.\n\nMaraming salamat,\nB-READY Disaster Preparedness";
                $gmailComposeUrl = "https://mail.google.com/mail/?view=cm&fs=1&to=" . urlencode($newU['email']) . "&su=" . urlencode("B-READY Platform - Your Login Credentials") . "&body=" . urlencode($mailBody);
            @endphp
            <div class="p-6 rounded-2xl bg-emerald-50/90 border border-emerald-300 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="p-2.5 rounded-xl bg-emerald-600 text-white font-bold shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-extrabold text-emerald-950">
                                    Bagong Account para kay {{ $newU['name'] }} ({{ ucfirst($newU['role']) }})
                                </h3>
                                @if (!empty($newU['mail_sent']))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-200 text-emerald-900">
                                        Email Sent ({{ strtoupper($newU['mail_method'] ?? 'Brevo') }})
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-200 text-amber-900">
                                        Manual Copy Ready
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-emerald-800 mt-0.5">
                                {{ $newU['mail_message'] }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0 flex-wrap">
                        <a href="{{ $gmailComposeUrl }}" target="_blank"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-emerald-300 hover:bg-emerald-50 text-emerald-800 text-xs font-bold transition shadow-2xs">
                            <svg class="w-4 h-4 text-rose-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 5.457v13.909c0 .904-.732 1.636-1.636 1.636h-3.819V11.73L12 16.64l-6.545-4.91v9.272H1.636A1.636 1.636 0 0 1 0 19.366V5.457c0-2.023 2.309-3.178 3.927-1.964L5.455 4.64 12 9.548l6.545-4.91 1.528-1.145C21.69 2.28 24 3.434 24 5.457z"/>
                            </svg>
                            <span>Buksan sa Gmail Web &rarr;</span>
                        </a>
                        <button type="button" 
                                onclick="copyCredentials('{{ $newU['email'] }}', '{{ $newU['password'] }}', '{{ $newU['login_url'] }}', '{{ ucfirst($newU['role']) }}')"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <span id="copy-btn-text">Kopyahin ang Login Link &amp; Details</span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-2 text-xs">
                    <div class="p-3 rounded-xl bg-white border border-emerald-200">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Login Email (Gmail)</span>
                        <span class="font-bold text-slate-800 select-all">{{ $newU['email'] }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-white border border-emerald-200">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Temporary Password</span>
                        <span class="font-mono font-bold text-slate-800 select-all">{{ $newU['password'] }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-white border border-emerald-200">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Direct Login URL</span>
                        <a href="{{ $newU['login_url'] }}" target="_blank" class="font-semibold text-emerald-600 hover:underline truncate block">
                            {{ $newU['login_url'] }} &nearr;
                        </a>
                    </div>
                </div>

                <p class="text-[11px] text-emerald-800 italic">
                    Paalala: Kapag nag-sign in ang user gamit ang temporary password, awtomatiko silang hihingan ng system na magpalit ng kanilang bagong personal password.
                </p>
            </div>
        @endif

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
                            <th class="py-3.5 px-4">Portal Access &amp; Security</th>
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
                                    <div class="flex flex-col gap-1.5">
                                        <div>
                                            @if ($user->must_change_password)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                    First Login: Password Change Required
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/70">
                                                    Password Verified
                                                </span>
                                            @endif
                                        </div>
                                        <div>
                                            <button type="button" 
                                                    onclick="copyTextToClipboard('{{ route('login') }}', this, 'Na-kopya!')"
                                                    class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 hover:text-emerald-900 hover:underline">
                                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                </svg>
                                                <span>Kopyahin ang Login Link</span>
                                            </button>
                                        </div>
                                    </div>
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

    <script>
        function copyTextToClipboard(text, element, successMsg = 'Na-kopya na!') {
            navigator.clipboard.writeText(text).then(() => {
                const originalHtml = element.innerHTML;
                element.innerHTML = `<span class="text-emerald-700 font-bold">✓ ${successMsg}</span>`;
                setTimeout(() => {
                    element.innerHTML = originalHtml;
                }, 2500);
            });
        }

        function copyCredentials(email, pass, url, role) {
            const text = `Magandang araw!\n\nNagawa na ang iyong opisyal na B-READY account bilang ${role}.\n\nNarito ang iyong login details:\n• Email: ${email}\n• Temporary Password: ${pass}\n• Portal Login Link: ${url}\n\nPaalala: Sa iyong unang pag-log in, hihilingan ka ng system na magtakda ng iyong bagong personal password bago ma-access ang dashboard.\n\nMaraming salamat,\nB-READY Disaster Preparedness`;
            navigator.clipboard.writeText(text).then(() => {
                const btn = document.getElementById('copy-btn-text');
                if (btn) {
                    btn.innerText = 'Na-kopya na ang Buong Mensahe!';
                    setTimeout(() => { btn.innerText = 'Kopyahin ang Login Link & Details'; }, 3000);
                }
            });
        }
    </script>
</x-layouts.admin>
