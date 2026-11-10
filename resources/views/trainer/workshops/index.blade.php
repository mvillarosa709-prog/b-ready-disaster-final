<x-layouts.trainer>
    <x-slot name="title">Workshop Management — B-READY</x-slot>

    <div class="border-b border-slate-200 bg-white px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto flex max-w-7xl flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <span class="rounded-md border border-emerald-200 bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">Trainer Portal</span>
                <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Workshop Management</h1>
                <p class="mt-1 text-sm text-slate-500">Schedule workshops for your courses and publish them once the course content is ready.</p>
            </div>
            <a href="{{ route('trainer.workshops.create') }}" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                <span class="mr-2 text-lg">+</span> Create Workshop
            </a>
        </div>
    </div>

    <div class="mx-auto mt-8 max-w-7xl space-y-4 px-4 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif
        @if ($errors->has('workshop'))
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ $errors->first('workshop') }}</div>
        @endif

        @if ($workshops->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center shadow-sm">
                <h2 class="font-bold text-slate-900">No workshops yet</h2>
                <p class="mt-1 text-sm text-slate-500">Create a workshop from one of your courses. Draft courses can have draft workshops while you finish preparing the content.</p>
                <a href="{{ route('trainer.workshops.create') }}" class="mt-5 inline-flex rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">Create your first workshop</a>
            </div>
        @else
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-3.5">Workshop</th>
                                <th class="px-5 py-3.5">Course</th>
                                <th class="px-5 py-3.5">Schedule</th>
                                <th class="px-5 py-3.5">Teachers</th>
                                <th class="px-5 py-3.5">Status</th>
                                <th class="px-5 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($workshops as $workshop)
                                <tr class="hover:bg-slate-50/70">
                                    <td class="px-5 py-4">
                                        <a href="{{ route('trainer.workshops.show', $workshop) }}" class="font-bold text-slate-900 hover:text-emerald-700 transition">
                                            {{ $workshop->title }}
                                        </a>
                                        <div class="mt-1 text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($workshop->description, 90) }}</div>
                                    </td>
                                    <td class="px-5 py-4 text-slate-700">{{ $workshop->course->title }}</td>
                                    <td class="px-5 py-4 text-xs text-slate-600">
                                        {{ $workshop->start_date->format('M d, Y') }} – {{ $workshop->end_date->format('M d, Y') }}
                                        <div class="mt-1 text-slate-400">Register by {{ $workshop->registration_deadline->format('M d, Y') }}</div>
                                    </td>
                                    <td class="px-5 py-4 text-slate-700">{{ $workshop->workshop_teachers_count }}</td>
                                    <td class="px-5 py-4">
                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $workshop->status === 'published' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ ucfirst($workshop->status) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-end gap-3">
                                            <a href="{{ route('trainer.workshops.show', $workshop) }}" class="text-xs font-semibold text-slate-700 hover:text-emerald-700">View</a>
                                            <a href="{{ route('trainer.workshops.edit', $workshop) }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900">Edit</a>
                                            <form method="POST" action="{{ route('trainer.workshops.destroy', $workshop) }}" onsubmit="return confirm('Delete this workshop?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-layouts.trainer>
