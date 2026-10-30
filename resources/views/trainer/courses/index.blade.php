<x-layouts.trainer>
    <x-slot name="title">Trainer Courses — B-READY</x-slot>

    <!-- Header Banner -->
    <div class="bg-white border-b border-slate-200/80 px-4 py-8 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        Course Management
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2 tracking-tight">
                    Disaster Preparedness Courses
                </h1>
                <p class="text-slate-500 text-sm mt-1">
                    Create and manage curricula, sequential learning modules, and classroom resources for teachers.
                </p>
            </div>
            <div>
                <a href="{{ route('trainer.courses.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Create New Course
                </a>
            </div>
        </div>
    </div>

    <!-- Courses Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        @if ($courses->isNotEmpty())
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50/80 text-slate-500 uppercase text-xs font-semibold">
                            <tr>
                                <th class="px-6 py-3.5 text-left">Course Details</th>
                                <th class="px-6 py-3.5 text-left">Estimated Duration</th>
                                <th class="px-6 py-3.5 text-left">Modules</th>
                                <th class="px-6 py-3.5 text-left">Publication Status</th>
                                <th class="px-6 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($courses as $course)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-900 text-base">
                                            {{ $course->title }}
                                        </div>
                                        <div class="text-xs text-slate-500 line-clamp-1 mt-1">
                                            {{ Str::limit($course->description, 100) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-700 font-medium">
                                        {{ $course->estimated_duration }} minutes
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                            {{ $course->modules->count() }} modules
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($course->status === 'published')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                                Published
                                            </span>
                                        @elseif ($course->status === 'unpublished')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-800">
                                                Unpublished
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                                Draft
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-3">
                                            <a href="{{ route('trainer.courses.show', $course->id) }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700">
                                                View Modules
                                            </a>
                                            <a href="{{ route('trainer.courses.edit', $course->id) }}" class="text-sm font-semibold text-slate-600 hover:text-slate-800">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('trainer.courses.destroy', $course->id) }}" onsubmit="return confirm('Are you sure you want to delete this course?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-800">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center shadow-xs">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-slate-100 text-slate-400 mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <p class="text-base font-bold text-slate-800">You don't have any courses yet.</p>
                <p class="text-sm text-slate-500 mt-1">Create your first disaster preparedness training course to get started.</p>
                <div class="mt-5">
                    <a href="{{ route('trainer.courses.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 shadow-sm transition">
                        Create Your First Course
                    </a>
                </div>
            </div>
        @endif
    </div>
</x-layouts.trainer>