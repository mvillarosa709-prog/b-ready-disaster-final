<x-app-layout>

    <x-slot name="header">

        <div class="flex items-center justify-between">

            <div>

                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('My Courses') }}
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Manage your training courses.
                </p>

            </div>


            <a
                href="{{ route('trainer.courses.create') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-800
                       border border-transparent rounded-md font-semibold
                       text-xs text-white uppercase tracking-widest
                       hover:bg-gray-700"
            >
                + Create Course
            </a>

        </div>

    </x-slot>


    <div class="py-10">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">


            {{-- Success Message --}}
            @if (session('success'))

                <div class="mb-6 bg-green-100 border border-green-200
                            text-green-800 px-4 py-3 rounded-lg">

                    {{ session('success') }}

                </div>

            @endif


            {{-- Courses --}}
            <div class="bg-white shadow-sm sm:rounded-lg">

                <div class="p-6">


                    <div class="mb-6">

                        <h3 class="text-lg font-semibold text-gray-800">
                            My Courses
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Courses created by you.
                        </p>

                    </div>


                    @if ($courses->count() > 0)

                        <div class="overflow-x-auto">

                            <table class="min-w-full divide-y divide-gray-200">

                                {{-- Table Header --}}
                                <thead class="bg-gray-50">

                                    <tr>

                                        {{-- Course --}}
                                        <th class="px-6 py-3 text-left text-xs font-medium
                                                   text-gray-500 uppercase tracking-wider">
                                            Course
                                        </th>


                                        {{-- Duration --}}
                                        <th class="px-6 py-3 text-left text-xs font-medium
                                                   text-gray-500 uppercase tracking-wider">
                                            Duration
                                        </th>


                                        {{-- Modules --}}
                                        <th class="px-6 py-3 text-left text-xs font-medium
                                                   text-gray-500 uppercase tracking-wider">
                                            Modules
                                        </th>


                                        {{-- Status --}}
                                        <th class="px-6 py-3 text-left text-xs font-medium
                                                   text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>


                                        {{-- Actions --}}
                                        <th class="px-6 py-3 text-left text-xs font-medium
                                                   text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>

                                    </tr>

                                </thead>


                                {{-- Table Body --}}
                                <tbody class="bg-white divide-y divide-gray-200">

                                    @foreach ($courses as $course)

                                        <tr>


                                            {{-- Course --}}
                                            <td class="px-6 py-4">

                                                <div class="font-medium text-gray-900">
                                                    {{ $course->title }}
                                                </div>

                                                <div class="text-sm text-gray-500 mt-1">
                                                    {{ Str::limit($course->description, 80) }}
                                                </div>

                                            </td>


                                            {{-- Duration --}}
                                            <td class="px-6 py-4 text-sm text-gray-700">

                                                {{ $course->estimated_duration }} minutes

                                            </td>


                                            {{-- Modules --}}
                                            <td class="px-6 py-4 text-sm text-gray-700">

                                                {{ $course->modules->count() }}

                                            </td>


                                            {{-- Status --}}
                                            <td class="px-6 py-4">

                                                @if ($course->status === 'published')

                                                    <span class="px-2 py-1 text-xs font-semibold
                                                                 rounded-full bg-green-100
                                                                 text-green-800">
                                                        Published
                                                    </span>

                                                @elseif ($course->status === 'unpublished')

                                                    <span class="px-2 py-1 text-xs font-semibold
                                                                 rounded-full bg-gray-100
                                                                 text-gray-800">
                                                        Unpublished
                                                    </span>

                                                @else

                                                    <span class="px-2 py-1 text-xs font-semibold
                                                                 rounded-full bg-yellow-100
                                                                 text-yellow-800">
                                                        Draft
                                                    </span>

                                                @endif

                                            </td>


                                            {{-- Actions --}}
                                            <td class="px-6 py-4">

                                                <div class="flex items-center gap-3">


                                                    {{-- View --}}
                                                    <a
                                                        href="{{ route('trainer.courses.show', $course->id) }}"
                                                        class="text-sm font-medium text-indigo-600
                                                               hover:text-indigo-800"
                                                    >
                                                        View
                                                    </a>


                                                    {{-- Edit --}}
                                                    <a
                                                        href="{{ route('trainer.courses.edit', $course->id) }}"
                                                        class="text-sm font-medium text-gray-600
                                                               hover:text-gray-900"
                                                    >
                                                        Edit
                                                    </a>


                                                    {{-- Delete --}}
                                                    <form
                                                        method="POST"
                                                        action="{{ route('trainer.courses.destroy', $course->id) }}"
                                                        onsubmit="return confirm('Are you sure you want to delete this course?');"
                                                    >

                                                        @csrf

                                                        @method('DELETE')

                                                        <button
                                                            type="submit"
                                                            class="text-sm font-medium text-red-600
                                                                   hover:text-red-800"
                                                        >
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


                    @else


                        {{-- No Courses --}}
                        <div class="text-center py-12">

                            <p class="text-gray-500">
                                You don't have any courses yet.
                            </p>

                            <p class="text-sm text-gray-400 mt-2">
                                Create your first training course to get started.
                            </p>


                            <div class="mt-5">

                                <a
                                    href="{{ route('trainer.courses.create') }}"
                                    class="inline-flex items-center px-4 py-2 bg-gray-800
                                           border border-transparent rounded-md
                                           font-semibold text-xs text-white uppercase
                                           tracking-widest hover:bg-gray-700"
                                >
                                    Create Your First Course
                                </a>

                            </div>

                        </div>


                    @endif


                </div>

            </div>

        </div>

    </div>

</x-app-layout>