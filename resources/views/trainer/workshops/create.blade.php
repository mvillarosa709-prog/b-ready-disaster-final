<x-layouts.trainer>
    <x-slot name="title">Create Workshop — B-READY</x-slot>

    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('trainer.workshops.index') }}" class="text-sm font-semibold text-emerald-700 hover:text-emerald-900">&larr; Workshop Management</a>
            <h1 class="mt-3 text-2xl font-extrabold text-slate-900">Create Workshop</h1>
            <p class="mt-1 text-sm text-slate-500">Choose one of your courses and set the schedule and registration deadline.</p>
        </div>
        <form method="POST" action="{{ route('trainer.workshops.store') }}" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            @include('trainer.workshops._form', ['workshop' => null])
        </form>
    </div>
</x-layouts.trainer>
