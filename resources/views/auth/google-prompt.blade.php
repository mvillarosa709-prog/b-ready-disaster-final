<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign in with Google — B-READY</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center p-4 font-sans text-slate-800 antialiased" x-data="{ showNewAccount: false }">

    <div class="w-full max-w-md bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden">
        
        <!-- Google Header -->
        <div class="px-8 pt-8 pb-4 text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-50 border border-slate-100 shadow-xs mb-3">
                <svg class="w-7 h-7" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
                </svg>
            </div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Sign in with Google</h1>
            <p class="text-sm text-slate-500 mt-1">
                Choose an account to continue to <span class="font-semibold text-slate-700">B-READY Disaster Preparedness</span>
            </p>
        </div>

        <!-- Account List -->
        <div class="px-6 py-2 divide-y divide-slate-100 max-h-80 overflow-y-auto" x-show="!showNewAccount">
            @forelse ($users as $account)
                <form method="POST" action="{{ route('auth.google.select') }}" class="w-full">
                    @csrf
                    <input type="hidden" name="email" value="{{ $account->email }}">
                    <button type="submit" class="w-full flex items-center justify-between p-3.5 rounded-2xl hover:bg-slate-50 transition text-left group">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shrink-0 {{ $account->role === 'trainer' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ strtoupper(substr($account->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-900 truncate group-hover:text-emerald-700 transition">
                                    {{ $account->name }}
                                </p>
                                <p class="text-xs text-slate-500 truncate">
                                    {{ $account->email }}
                                </p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold shrink-0 {{ $account->role === 'trainer' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                            {{ ucfirst($account->role) }}
                        </span>
                    </button>
                </form>
            @empty
                <div class="py-6 text-center text-sm text-slate-500">
                    No accounts yet. Click below to add your Google account.
                </div>
            @endforelse
        </div>

        <!-- Use Another Account Option -->
        <div class="px-6 py-3 border-t border-slate-100" x-show="!showNewAccount">
            <button
                type="button"
                @click="showNewAccount = true"
                class="w-full flex items-center gap-3.5 p-3 rounded-2xl hover:bg-slate-50 transition text-left text-sm font-medium text-slate-700 hover:text-emerald-700"
            >
                <div class="w-10 h-10 rounded-full border border-dashed border-slate-300 flex items-center justify-center text-slate-400 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <span>Use another Google account</span>
            </button>
        </div>

        <!-- New Account Form with Role Selection -->
        <div class="p-6 border-t border-slate-100" x-show="showNewAccount" x-cloak>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-slate-900">Sign in with a new Google Account</h3>
                <button type="button" @click="showNewAccount = false" class="text-xs text-slate-500 hover:text-slate-800">
                    ← Back to accounts
                </button>
            </div>

            <form method="POST" action="{{ route('auth.google.select') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Full Name</label>
                    <input type="text" name="name" placeholder="e.g. Juan Dela Cruz" required class="w-full text-sm rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500/20 py-2 px-3">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Google Email Address</label>
                    <input type="email" name="email" placeholder="juandelacruz@gmail.com" required class="w-full text-sm rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500/20 py-2 px-3">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Select Role in B-READY</label>
                    <select name="role" required class="w-full text-sm rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500/20 py-2 px-3">
                        <option value="teacher">Teacher (Educator / School Staff)</option>
                        <option value="trainer">DRR Trainer (Course / Workshop Creator)</option>
                    </select>
                    <p class="text-[11px] text-slate-500 mt-1">This sets whether you enter the Teacher portal or the Trainer management portal.</p>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm transition shadow-sm shadow-emerald-600/30">
                    Continue to B-READY
                </button>
            </form>
        </div>

        <!-- Footer / Cancel -->
        <div class="px-8 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>To continue, Google will share your name and email with B-READY.</span>
            <a href="{{ route('login') }}" class="font-medium text-slate-700 hover:text-emerald-700 shrink-0 ml-4">
                Cancel
            </a>
        </div>

    </div>

</body>
</html>

