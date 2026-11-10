<x-layouts.trainer>
    <x-slot name="title">Create Workshop — B-READY</x-slot>

    <!-- Header Banner -->
    <div class="bg-white border-b border-slate-200/80 px-4 py-6 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <!-- Clean Breadcrumbs -->
                <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2">
                    <a href="{{ route('trainer.workshops.index') }}" class="hover:text-emerald-600 transition font-medium">
                        Workshop Management
                    </a>
                    <span>/</span>
                    <span class="text-slate-800 font-semibold">New Workshop</span>
                </nav>

                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
                        {{ __('Create Workshop') }}
                    </h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                        Draft Mode
                    </span>
                </div>
                <p class="text-sm text-slate-500 mt-1">
                    Schedule a training session, attach a curriculum course, and set registration deadlines for teachers.
                </p>
            </div>

            <!-- Single Back Action on the right side -->
            <div class="flex items-center gap-2">
                <a
                    href="{{ route('trainer.workshops.index') }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-400 transition shadow-xs"
                >
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Workshops
                </a>
            </div>
        </div>
    </div>

    <!-- Workshop Content Layout -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form method="POST" action="{{ route('trainer.workshops.store') }}">
            @include('trainer.workshops._form', ['workshop' => null])
        </form>
    </div>
</x-layouts.trainer>
