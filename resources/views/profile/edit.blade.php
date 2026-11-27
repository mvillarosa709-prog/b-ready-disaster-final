```blade
@php
    if (auth()->user()->role === 'admin') {
        $portalLayout = 'layouts.admin';
    } elseif (auth()->user()->role === 'trainer') {
        $portalLayout = 'layouts.trainer';
    } else {
        $portalLayout = 'layouts.teacher';
    }
@endphp

<x-dynamic-component :component="$portalLayout">
    <x-slot name="title">Profile &amp; Change Password — B-READY</x-slot>

    <!-- Header Banner -->
    <div class="bg-white border-b border-slate-200/80">
        <div class="w-full px-4 py-7 sm:px-6 lg:px-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

            <div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    Account Security
                </span>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2 tracking-tight">
                    Profile &amp; Change Password
                </h1>

                <p class="text-slate-500 text-sm mt-1">
                    Manage your account details, email address, password credentials, and active login session.
                </p>
            </div>

            <!-- Single Log Out Button -->
            <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                @csrf

                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-rose-600 text-white text-sm font-semibold shadow-sm hover:bg-rose-700 transition duration-200"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                        />
                    </svg>
                    Log Out
                </button>
            </form>

        </div>
    </div>

    <!-- Profile Settings -->
    <div class="w-full px-4 sm:px-6 lg:px-8 mt-6 space-y-6">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

            <!-- Profile Information -->
            <div class="p-6 sm:p-7 bg-white border border-slate-200/80 shadow-xs rounded-2xl">
                @include('profile.partials.update-profile-information-form')
            </div>

            <!-- Update Password -->
            <div class="p-6 sm:p-7 bg-white border border-slate-200/80 shadow-xs rounded-2xl">
                @include('profile.partials.update-password-form')
            </div>

        </div>

        <!-- Delete Account -->
        <div class="p-6 sm:p-7 bg-white border border-rose-200/80 shadow-xs rounded-2xl">
            <div class="max-w-2xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>

    </div>
</x-dynamic-component>
```