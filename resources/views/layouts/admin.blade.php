<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Super Admin Portal' }} — B-READY Disaster Preparedness</title>

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
                                <span class="block text-xs text-emerald-400 font-medium">Super Admin Portal</span>
                            </div>
                        </div>
                        <nav class="flex flex-1 flex-col">
                            <ul role="list" class="flex flex-1 flex-col gap-y-7">
                                <li>
                                    <ul role="list" class="-mx-2 space-y-1">
                                        @include('layouts.admin-nav-links')
                                    </ul>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Static Desktop Sidebar (Identical structure to Teacher and Trainer) -->
        <aside class="hidden lg:fixed lg:inset-y-0 lg:z-40 lg:flex lg:w-72 lg:flex-col border-r border-slate-200/80 bg-white">
            <div class="flex grow flex-col gap-y-5 overflow-y-auto px-6 pb-4">
                <!-- Branding -->
                <div class="flex h-20 shrink-0 items-center justify-between border-b border-slate-100">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                        <x-application-logo class="h-14 w-[4.75rem] shrink-0" />
                        <div class="min-w-0">
                            <span class="flex items-center gap-1.5 text-xs text-emerald-600 font-semibold uppercase tracking-wider">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Super Admin Portal
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Navigation List -->
                <nav class="flex flex-1 flex-col pt-2">
                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                        <li>
                            <ul role="list" class="-mx-2 space-y-1.5">
                                @include('layouts.admin-nav-links')
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Main Layout Area -->
        <div class="flex flex-1 flex-col lg:pl-72 min-w-0">
            <!-- Top Navigation Bar (Mobile & Desktop) -->
            <header class="sticky top-0 z-30 flex h-16 lg:h-20 shrink-0 items-center justify-between gap-x-4 border-b border-slate-200/80 bg-white/90 backdrop-blur-md px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <!-- Hamburger Button (Mobile) -->
                    <button type="button" class="-m-2.5 p-2.5 text-slate-700 lg:hidden" @click="sidebarOpen = true">
                        <span class="sr-only">Open sidebar</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    <div class="flex items-center gap-2.5">
                        <span class="text-base sm:text-lg font-extrabold text-slate-900 tracking-tight">B-READY Super Admin Workspace</span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('profile.edit') }}" title="{{ ucwords(auth()->user()->name) }} — Profile &amp; Account Settings" class="group inline-flex items-center gap-2.5 px-4 py-2 rounded-xl border border-slate-200/90 bg-slate-50/80 hover:bg-emerald-50/80 hover:border-emerald-300 transition text-sm sm:text-base font-bold text-slate-800 hover:text-emerald-800 max-w-fit cursor-pointer shadow-2xs">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="capitalize whitespace-nowrap">{{ ucwords(auth()->user()->name) }}</span>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-emerald-600 group-hover:translate-x-0.5 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 pb-16">
                @isset($header)
                    <div class="bg-white border-b border-slate-200/80">
                        <div class="w-full px-4 py-7 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </div>
                @endisset

                <!-- Flash Messages -->
                @if (session('success'))
                    <div class="w-full px-4 sm:px-6 lg:px-8 mt-6">
                        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-xs">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm font-medium">{{ session('success') }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="w-full px-4 sm:px-6 lg:px-8 mt-6">
                        <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 flex items-center justify-between shadow-xs">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-red-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm font-medium">{{ session('error') }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
