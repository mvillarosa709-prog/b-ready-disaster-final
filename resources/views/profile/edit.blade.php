@php
    $portalLayout = in_array(auth()->user()->role, ['trainer', 'admin'], true) ? 'layouts.trainer' : 'layouts.teacher';
    $backRoute = auth()->user()->role === 'trainer'
        ? route('trainer.dashboard')
        : (auth()->user()->role === 'admin' ? route('trainer.certificates.pending') : route('teacher.dashboard'));
@endphp

<x-dynamic-component :component="$portalLayout">
    <x-slot name="title">Account Settings &amp; Change Password — B-READY</x-slot>

    <!-- Header Banner -->
    <div class="bg-white border-b border-slate-200/80 px-4 py-8 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    Account Security
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2 tracking-tight">
                    Profile &amp; Change Password
                </h1>
                <p class="text-slate-500 text-sm mt-1">
                    Update your account details, email address, and password.
                </p>
            </div>
            <div>
                <a href="{{ $backRoute }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 shadow-xs transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <div class="p-6 sm:p-8 bg-white border border-slate-200/80 shadow-xs rounded-2xl">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="p-6 sm:p-8 bg-white border border-slate-200/80 shadow-xs rounded-2xl">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="p-6 sm:p-8 bg-white border border-slate-200/80 shadow-xs rounded-2xl">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-dynamic-component>
