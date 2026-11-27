<x-layouts.admin>
    <x-slot name="title">Edit User — {{ $user->name }}</x-slot>

    <!-- Header Banner -->
    <div class="bg-white border-b border-slate-200/80">
        <div class="w-full px-4 py-7 sm:px-6 lg:px-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-700 mb-2">
                    &larr; Back to User Directory
                </a>
                <div class="flex items-center gap-2.5">
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Edit User Account &amp; Role</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold 
                        {{ $user->role === 'admin' ? 'bg-slate-900 text-emerald-400' : ($user->role === 'trainer' ? 'bg-emerald-100 text-emerald-800' : 'bg-sky-100 text-sky-800') }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
                <p class="text-sm text-slate-500 mt-0.5">Modifying user credentials and assigned system role for <strong>{{ $user->email }}</strong>.</p>
            </div>
        </div>
    </div>

    <div class="w-full px-4 sm:px-6 lg:px-8 mt-6 max-w-5xl">
        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs shadow-xs">
                <p class="font-bold mb-1">Please fix the following validation errors:</p>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Account Credentials -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-5">
                <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">1. Account Details</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Full Name *</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required 
                               class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500/20">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Email Address *</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required 
                               class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500/20">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Change Password (leave blank to keep current)</label>
                        <input type="password" name="password" 
                               placeholder="Enter new password (min. 8 chars)"
                               class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500/20">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Account Status</label>
                        <label class="flex items-center gap-2.5 mt-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" 
                                   @checked(old('is_active', $user->is_active ?? true))
                                   @disabled($user->id === auth()->id())
                                   class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="text-xs font-semibold text-slate-700">Account is Active</span>
                        </label>
                        @if ($user->id === auth()->id())
                            <span class="text-[11px] text-slate-400 block mt-1">You cannot deactivate your own active session.</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Role Selection -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-5">
                <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">2. Assigned Role</h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <label class="relative flex flex-col p-4 rounded-xl border border-slate-200 bg-slate-50/50 cursor-pointer hover:bg-slate-50 transition">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900">Teacher</span>
                            <input type="radio" name="role" value="teacher" 
                                   @checked(old('role', $user->role) === 'teacher')
                                   onchange="handleRoleChange('teacher')"
                                   class="text-emerald-600 focus:ring-emerald-500">
                        </div>
                        <p class="text-[11px] text-slate-500 mt-2">Sequential learning, classroom packages, student drills, auto certificate.</p>
                    </label>

                    <label class="relative flex flex-col p-4 rounded-xl border border-slate-200 bg-slate-50/50 cursor-pointer hover:bg-slate-50 transition">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900">DRR Trainer</span>
                            <input type="radio" name="role" value="trainer" 
                                   @checked(old('role', $user->role) === 'trainer')
                                   onchange="handleRoleChange('trainer')"
                                   class="text-emerald-600 focus:ring-emerald-500">
                        </div>
                        <p class="text-[11px] text-slate-500 mt-2">Workshop cohorts, courses, quizzes, and learner progress tracking.</p>
                    </label>

                    <label class="relative flex flex-col p-4 rounded-xl border border-slate-200 bg-slate-50/50 cursor-pointer hover:bg-slate-50 transition">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900">Super Administrator</span>
                            <input type="radio" name="role" value="admin" 
                                   @checked(old('role', $user->role) === 'admin')
                                   onchange="handleRoleChange('admin')"
                                   class="text-emerald-600 focus:ring-emerald-500">
                        </div>
                        <p class="text-[11px] text-slate-500 mt-2">Full system authority, roles, and security governance.</p>
                    </label>
                </div>
            </div>

            <!-- Submit Section -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-700 text-xs font-bold hover:bg-slate-50 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm shadow-emerald-600/30 transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
