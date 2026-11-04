<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Teacher Portal' }} — B-READY Disaster Preparedness</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-800" x-data="{ sidebarOpen: false }">
    <div class="min-h-full flex">
        <!-- Off-canvas mobile menu -->
        <div x-show="sidebarOpen" class="relative z-50 lg:hidden" role="dialog" aria-modal="true" style="display: none;">
            <div x-show="sidebarOpen" 
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm" @click="sidebarOpen = false"></div>

            <div class="fixed inset-0 flex">
                <div x-show="sidebarOpen" 
                     x-transition:enter="transition ease-in-out duration-300 transform"
                     x-transition:enter-start="-translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transition ease-in-out duration-300 transform"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="-translate-x-full"
                     class="relative mr-16 flex w-full max-w-xs flex-1">
                    <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                        <button type="button" class="-m-2.5 p-2.5 text-white" @click="sidebarOpen = false">
                            <span class="sr-only">Close sidebar</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Sidebar component for mobile -->
                    <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-slate-900 px-6 pb-4">
                        <div class="flex h-16 shrink-0 items-center gap-3">
                            <x-application-logo class="h-12 w-16 shrink-0" />
                            <div>
                                <span class="block text-xs text-emerald-400 font-medium">Teacher Portal</span>
                            </div>
                        </div>
                        <nav class="flex flex-1 flex-col">
                            <ul role="list" class="flex flex-1 flex-col gap-y-7">
                                <li>
                                    <ul role="list" class="-mx-2 space-y-1">
                                        @include('layouts.teacher-nav-links')
                                    </ul>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Static Desktop Sidebar -->
        <aside class="hidden lg:fixed lg:inset-y-0 lg:z-40 lg:flex lg:w-72 lg:flex-col border-r border-slate-200/80 bg-white">
            <div class="flex grow flex-col gap-y-5 overflow-y-auto px-6 pb-4">
                <!-- Branding -->
                <div class="flex h-20 shrink-0 items-center justify-between border-b border-slate-100">
                    <a href="{{ route('teacher.dashboard') }}" class="flex items-center gap-3">
                        <x-application-logo class="h-14 w-[4.75rem] shrink-0" />
                        <div class="min-w-0">
                            <span class="flex items-center gap-1.5 text-xs text-emerald-600 font-semibold uppercase tracking-wider">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Teacher Portal
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Navigation List -->
                <nav class="flex flex-1 flex-col">
                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider px-3 mb-2">Navigation</div>
                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                        <li>
                            <ul role="list" class="-mx-2 space-y-1.5">
                                @include('layouts.teacher-nav-links')
                            </ul>
                        </li>

                        <!-- Quick Info Card -->
                        <li class="mt-auto">
                            <div class="rounded-xl bg-gradient-to-br from-emerald-50 to-slate-100 p-4 border border-emerald-100/60">
                                <div class="flex items-center gap-2 text-emerald-900 font-semibold text-xs tracking-wide uppercase">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    DRR Preparedness
                                </div>
                                <p class="text-xs text-slate-600 mt-1.5 leading-relaxed">
                                    Complete workshop modules to unlock official certification and classroom activity packs.
                                </p>
                            </div>
                        </li>

                        <!-- User Profile card -->
                        <li class="border-t border-slate-100 pt-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-sm border border-emerald-200">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="text-sm font-semibold text-slate-800 truncate">{{ auth()->user()->name }}</div>
                                        <div class="text-xs text-slate-500 capitalize">Educator / Teacher</div>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" title="Sign out" class="text-slate-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-rose-50 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Main Layout Area -->
        <div class="lg:pl-72 flex flex-col flex-1 min-w-0">
            <!-- Sticky Top Header -->
            <header class="sticky top-0 z-30 grid h-16 shrink-0 grid-cols-[auto_minmax(0,1fr)] items-center gap-x-3 border-b border-slate-200/80 bg-white/90 px-4 shadow-xs backdrop-blur-md sm:px-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,2fr)_minmax(0,1fr)] lg:px-8">
                <!-- Hamburger Button (Mobile) -->
                <button type="button" class="-m-2.5 p-2.5 text-slate-700 lg:hidden" @click="sidebarOpen = true">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <!-- Centered Workshop Discovery Action -->
                <div class="col-start-2 flex w-full items-center justify-center lg:col-start-2">
                    <a href="{{ route('teacher.workshops.index') }}"
                       class="inline-flex h-10 w-full max-w-sm items-center justify-center gap-2 rounded-lg border border-emerald-600 bg-white px-3 text-xs font-bold text-emerald-700 shadow-sm transition hover:bg-emerald-50 focus:outline-hidden focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                        <svg class="h-4 w-4 shrink-0 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span>Find Workshops</span>
                    </a>

                    <!-- Account menu moved to the sidebar -->
                    <div class="hidden">
                        <button type="button"
                                class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-100 focus:outline-hidden transition-colors">
                            <div class="h-8 w-8 rounded-full bg-gradient-to-tr from-indigo-600 to-indigo-500 text-white font-bold flex items-center justify-center text-xs shadow-xs">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <span class="hidden md:inline-block text-xs font-semibold text-slate-700">{{ auth()->user()->name }}</span>
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-700"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 z-50 mt-2 w-48 origin-top-right rounded-xl bg-white py-1.5 shadow-lg border border-slate-100 focus:outline-hidden"
                             style="display: none;">
                            <div class="px-4 py-2 border-b border-slate-100">
                                <div class="text-xs text-slate-400 font-medium"></div>
                                <div class="text-xs font-bold text-slate-800 truncate"></div>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2 text-xs font-medium text-rose-600 hover:bg-rose-50">
                                    <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Container -->
            <main class="flex-1 px-4 pb-8 sm:px-6 lg:px-8">
                <div class="pt-24 pb-6">
                    <h1 class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">
                        {{ $header ?? 'Disaster Preparedness Learning Platform' }}
                    </h1>
                </div>
                <!-- Flash Alerts -->
                @if (session('success'))
                    <div class="mb-6 flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3.5 text-sm text-emerald-800 shadow-xs" role="alert">
                        <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="font-medium">{{ session('success') }}</div>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="mb-6 flex items-center gap-3 rounded-xl bg-amber-50 border border-amber-200 px-4 py-3.5 text-sm text-amber-800 shadow-xs" role="alert">
                        <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div class="font-medium">{{ session('warning') }}</div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 flex items-center gap-3 rounded-xl bg-rose-50 border border-rose-200 px-4 py-3.5 text-sm text-rose-800 shadow-xs" role="alert">
                        <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="font-medium">{{ session('error') }}</div>
                    </div>
                @endif

                @if (session('info'))
                    <div class="mb-6 flex items-center gap-3 rounded-xl bg-sky-50 border border-sky-200 px-4 py-3.5 text-sm text-sky-800 shadow-xs" role="alert">
                        <svg class="w-5 h-5 text-sky-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="font-medium">{{ session('info') }}</div>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
