<x-layouts.admin>
    <x-slot name="title">Create User Account — Super Admin</x-slot>

    <!-- Header Banner -->
    <div class="bg-white border-b border-slate-200/80">
        <div class="w-full px-4 py-7 sm:px-6 lg:px-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-700 mb-2">
                    &larr; Back to User Directory
                </a>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Provision New User Account</h1>
                <p class="text-sm text-slate-500 mt-0.5">Assign the primary system role and dispatch portal credentials directly to the user's Gmail.</p>
            </div>
        </div>
    </div>

    <div class="w-full px-4 sm:px-6 lg:px-8 mt-6 max-w-5xl">
        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs shadow-xs">
                <p class="font-bold mb-1">Please correct the following errors:</p>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
            @csrf

            <!-- Account Credentials & Identity -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-5">
                <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">1. Account Credentials & Identity</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Full Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required 
                               placeholder="e.g. Juan dela Cruz"
                               class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500/20">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Email Address (Login Username) *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required 
                               placeholder="e.g. user@example.com"
                               class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500/20">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Initial Password *</label>
                        <input type="password" name="password" required 
                               placeholder="Minimum 8 characters"
                               class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500/20">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Account Status</label>
                        <label class="flex items-center gap-2.5 mt-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}
                                   class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="text-xs font-semibold text-slate-700">Account is Active and permitted to log in</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Role Selection -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-5">
                <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">2. Primary System Role</h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <label class="relative flex flex-col p-4 rounded-xl border border-slate-200 bg-slate-50/50 cursor-pointer hover:bg-slate-50 transition">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900">Teacher</span>
                            <input type="radio" name="role" value="teacher" @checked(old('role', 'teacher') === 'teacher') 
                                   onchange="handleRoleChange('teacher')"
                                   class="text-emerald-600 focus:ring-emerald-500">
                        </div>
                        <p class="text-[11px] text-slate-500 mt-2">Enrolls in workshops, attends sequential modules, takes quizzes, gets instant certificate, and conducts drills.</p>
                    </label>

                    <label class="relative flex flex-col p-4 rounded-xl border border-slate-200 bg-slate-50/50 cursor-pointer hover:bg-slate-50 transition">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900">DRR Trainer</span>
                            <input type="radio" name="role" value="trainer" @checked(old('role') === 'trainer') 
                                   onchange="handleRoleChange('trainer')"
                                   class="text-emerald-600 focus:ring-emerald-500">
                        </div>
                        <p class="text-[11px] text-slate-500 mt-2">Creates courses, modules, materials, module quizzes, workshops, and oversees learner performance.</p>
                    </label>

                    <label class="relative flex flex-col p-4 rounded-xl border border-slate-200 bg-slate-50/50 cursor-pointer hover:bg-slate-50 transition">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900">Super Administrator</span>
                            <input type="radio" name="role" value="admin" @checked(old('role') === 'admin') 
                                   onchange="handleRoleChange('admin')"
                                   class="text-emerald-600 focus:ring-emerald-500">
                        </div>
                        <p class="text-[11px] text-slate-500 mt-2">Full system authority: manages users, grants roles, overrides permissions, and audits platform activities.</p>
                    </label>
                </div>
            </div>

            <!-- Section 3: Portal Login Link & Email Dispatch -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
                <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span>3. Portal Login Link &amp; Send to Gmail</span>
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Portal Login Link (For User)</label>
                        <div class="flex items-center gap-2">
                            <input type="text" readonly value="{{ route('login') }}" 
                                   class="w-full text-xs font-mono rounded-xl border-slate-200 bg-slate-50 text-slate-700 focus:outline-none">
                            <button type="button" 
                                    onclick="navigator.clipboard.writeText('{{ route('login') }}'); this.innerText='Copied!';"
                                    class="px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold hover:bg-slate-100 shrink-0">
                                Copy Link
                            </button>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">The user will use this link to sign in with their temporary credentials.</p>
                    </div>

                    <div class="flex flex-col justify-center">
                        <label class="flex items-center gap-2.5 p-3 rounded-xl border border-emerald-200 bg-emerald-50/60 cursor-pointer">
                            <input type="checkbox" name="send_gmail" value="1" checked
                                   class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <div>
                                <span class="text-xs font-bold text-emerald-950 block">Send Login Link &amp; Credentials to Gmail</span>
                                <span class="text-[11px] text-emerald-800">Automatically send credentials and direct login link to the user's Gmail.</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Submit Section -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-700 text-xs font-bold hover:bg-slate-50 transition">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm shadow-emerald-600/30 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    <span>Create User &amp; Send to Gmail</span>
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
