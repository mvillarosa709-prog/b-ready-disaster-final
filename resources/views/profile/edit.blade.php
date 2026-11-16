@php
    $portalLayout = in_array(auth()->user()->role, ['trainer', 'admin'], true) ? 'layouts.trainer' : 'layouts.teacher';
    $backRoute = auth()->user()->role === 'trainer'
        ? route('trainer.dashboard')
        : (auth()->user()->role === 'admin' ? route('trainer.certificates.pending') : route('teacher.dashboard'));
@endphp

<x-dynamic-component :component="$portalLayout">
    <x-slot name="title">Profile &amp; Change Password — B-READY</x-slot>

    <!-- Header Banner -->
    <div class="bg-white border-b border-slate-200/80">
        <div class="w-full px-4 py-7 sm:px-6 lg:px-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        Account Security
                    </span>
                    <span class="text-xs font-medium text-slate-400">Personal Settings</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2 tracking-tight">
                    Profile &amp; Change Password
                </h1>
                <p class="text-slate-500 text-sm mt-1">
                    Manage your account details, email address, password credentials, and active login session.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-rose-600 text-white text-sm font-semibold hover:bg-rose-700 shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="w-full px-4 sm:px-6 lg:px-8 mt-6 space-y-6">
        <!-- Top Account Summary & Session Bar -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="h-14 w-14 rounded-2xl bg-emerald-600 text-white font-extrabold text-xl flex items-center justify-center shadow-xs shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2.5">
                        <h2 class="text-lg font-bold text-slate-900">{{ $user->name }}</h2>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                            {{ $user->role === 'trainer' ? 'DRR Expert Facilitator' : ucfirst($user->role) }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">{{ $user->email }} &bull; Member since {{ $user->created_at?->format('M Y') ?? '2026' }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-3 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                <span class="text-xs font-medium text-slate-400 hidden md:inline">Signed in as {{ $user->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-rose-200 bg-rose-50/70 text-rose-700 text-xs font-semibold hover:bg-rose-100 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Sign Out of Session
                    </button>
                </form>
            </div>
        </div>

        <!-- Two-Column Settings Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
            <div class="p-6 sm:p-7 bg-white border border-slate-200/80 shadow-xs rounded-2xl">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="p-6 sm:p-7 bg-white border border-slate-200/80 shadow-xs rounded-2xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Danger Zone: Delete Account -->
        <div class="p-6 sm:p-7 bg-white border border-rose-200/80 shadow-xs rounded-2xl">
            <div class="max-w-2xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-dynamic-component>
