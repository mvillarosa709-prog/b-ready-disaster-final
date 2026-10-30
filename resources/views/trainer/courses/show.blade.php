<x-app-layout>

    <x-slot name="header">

        <div class="flex items-center justify-between">

            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Course Details') }}
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Manage your course, modules, and training materials.
                </p>
            </div>

            <a
                href="{{ route('trainer.courses.index') }}"
                class="px-4 py-2 bg-gray-800 text-white rounded-md
                       text-sm font-semibold hover:bg-gray-700"
            >
                Back to Courses
            </a>

        </div>

    </x-slot>


    <div class="py-10">

        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">


            {{-- Success Message --}}
            @if (session('success'))

                <div
                    class="mb-6 bg-green-100 border border-green-200
                           text-green-800 px-4 py-3 rounded-lg"
                >
                    {{ session('success') }}
                </div>

            @endif


            {{-- Course Information --}}
            <div class="bg-white shadow-sm sm:rounded-lg mb-8">

                <div class="p-6">

                    <div class="flex items-start justify-between gap-6">

                        <div>

                            <h1 class="text-2xl font-bold text-gray-900">
                                {{ $course->title }}
                            </h1>

                            <p class="mt-3 text-gray-600">
                                {{ $course->description }}
                            </p>

                        </div>


                        {{-- Status --}}
                        <div>

                            @if ($course->status === 'published')

                                <span
                                    class="inline-flex items-center px-3 py-1
                                           rounded-full text-xs font-semibold
                                           bg-green-100 text-green-800"
                                >
                                    Published
                                </span>

                            @elseif ($course->status === 'unpublished')

                                <span
                                    class="inline-flex items-center px-3 py-1
                                           rounded-full text-xs font-semibold
                                           bg-yellow-100 text-yellow-800"
                                >
                                    Unpublished
                                </span>

                            @else

                                <span
                                    class="inline-flex items-center px-3 py-1
                                           rounded-full text-xs font-semibold
                                           bg-gray-100 text-gray-800"
                                >
                                    Draft
                                </span>

                            @endif

                        </div>

                    </div>


                    {{-- Course Details --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">

                        <div class="bg-gray-50 rounded-lg p-4">

                            <p class="text-sm text-gray-500">
                                Learning Objectives
                            </p>

                            <p class="mt-2 text-sm text-gray-800">
                                {{ $course->learning_objectives }}
                            </p>

                        </div>


                        <div class="bg-gray-50 rounded-lg p-4">

                            <p class="text-sm text-gray-500">
                                Target Participants
                            </p>

                            <p class="mt-2 text-sm text-gray-800">
                                {{ $course->target_participants }}
                            </p>

                        </div>


                        <div class="bg-gray-50 rounded-lg p-4">

                            <p class="text-sm text-gray-500">
                                Estimated Duration
                            </p>

                            <p class="mt-2 text-sm text-gray-800">
                                {{ $course->estimated_duration }} minutes
                            </p>

                        </div>

                    </div>


                    {{-- Course Actions --}}
                    <div class="flex items-center gap-3 mt-8">

                        <a
                            href="{{ route(
                                'trainer.courses.edit',
                                $course->id
                            ) }}"
                            class="px-4 py-2 bg-indigo-600 text-white
                                   rounded-md text-sm font-semibold
                                   hover:bg-indigo-700"
                        >
                            Edit Course
                        </a>

                        <form
                            method="POST"
                            action="{{ route(
                                'trainer.courses.destroy',
                                $course->id
                            ) }}"
                            onsubmit="return confirm(
                                'Are you sure you want to delete this course?'
                            );"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="px-4 py-2 bg-red-600 text-white
                                       rounded-md text-sm font-semibold
                                       hover:bg-red-700"
                            >
                                Delete Course
                            </button>

                        </form>

                    </div>

                </div>

            </div>



            {{-- Modules Section --}}
            <div class="bg-white shadow-sm sm:rounded-lg mb-8">

                <div class="p-6">

                    <div class="flex items-center justify-between mb-6">

                        <div>

                            <h2 class="text-xl font-semibold text-gray-900">
                                Course Modules
                            </h2>

                            <p class="text-sm text-gray-500 mt-1">
                                Organize the learning modules for this course.
                            </p>

                        </div>

                        <a
                            href="{{ route(
                                'trainer.modules.create',
                                $course->id
                            ) }}"
                            class="px-4 py-2 bg-gray-800 text-white
                                   rounded-md text-sm font-semibold
                                   hover:bg-gray-700"
                        >
                            + Add Module
                        </a>

                    </div>


                    @if ($course->modules->count() > 0)

                        <div class="space-y-6">

                            @foreach ($course->modules as $module)

                                <div
                                    class="border border-gray-200
                                           rounded-lg p-5"
                                >

                                    {{-- Module Header --}}
                                    <div
                                        class="flex items-start
                                               justify-between gap-4"
                                    >

                                        <div>

                                            <div
                                                class="flex items-center
                                                       gap-3"
                                            >

                                                <h3
                                                    class="text-lg
                                                           font-semibold
                                                           text-gray-900"
                                                >
                                                    {{ $module->sequence }}.
                                                    {{ $module->title }}
                                                </h3>


                                                @if ($module->is_required)

                                                    <span
                                                        class="px-2 py-1
                                                               rounded-full
                                                               text-xs
                                                               font-semibold
                                                               bg-red-100
                                                               text-red-700"
                                                    >
                                                        Required
                                                    </span>

                                                @else

                                                    <span
                                                        class="px-2 py-1
                                                               rounded-full
                                                               text-xs
                                                               font-semibold
                                                               bg-gray-100
                                                               text-gray-700"
                                                    >
                                                        Optional
                                                    </span>

                                                @endif

                                            </div>


                                            <p
                                                class="text-sm text-gray-600
                                                       mt-2"
                                            >
                                                {{ $module->description }}
                                            </p>

                                            <p
                                                class="text-xs text-gray-500
                                                       mt-2"
                                            >
                                                Duration:
                                                {{ $module->estimated_duration }}
                                                minutes
                                            </p>

                                        </div>


                                        {{-- Module Actions --}}
                                        <div
                                            class="flex items-center
                                                   gap-2"
                                        >

                                            <a
                                                href="{{ route(
                                                    'trainer.modules.edit',
                                                    [
                                                        $course->id,
                                                        $module->id
                                                    ]
                                                ) }}"
                                                class="text-sm text-indigo-600
                                                       hover:text-indigo-800"
                                            >
                                                Edit
                                            </a>


                                            <form
                                                method="POST"
                                                action="{{ route(
                                                    'trainer.modules.destroy',
                                                    [
                                                        $course->id,
                                                        $module->id
                                                    ]
                                                ) }}"
                                                onsubmit="return confirm(
                                                    'Delete this module?'
                                                );"
                                            >

                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="text-sm text-red-600
                                                           hover:text-red-800"
                                                >
                                                    Delete
                                                </button>

                                            </form>

                                        </div>

                                    </div>



                                    {{-- Module Learning Objectives --}}
                                    <div
                                        class="mt-5 bg-gray-50 rounded-lg
                                               p-4"
                                    >

                                        <p
                                            class="text-sm font-semibold
                                                   text-gray-700"
                                        >
                                            Learning Objectives
                                        </p>

                                        <p
                                            class="text-sm text-gray-600
                                                   mt-1"
                                        >
                                            {{ $module->learning_objectives }}
                                        </p>

                                    </div>



                                    {{-- Training Materials --}}
                                    <div class="mt-6">

                                        <div
                                            class="flex items-center
                                                   justify-between mb-4"
                                        >

                                            <div>

                                                <h4
                                                    class="font-semibold
                                                           text-gray-900"
                                                >
                                                    Training Materials
                                                </h4>

                                                <p
                                                    class="text-xs
                                                           text-gray-500
                                                           mt-1"
                                                >
                                                    Files and external
                                                    resources for this module.
                                                </p>

                                            </div>


                                            <a
                                                href="{{ route(
                                                    'trainer.materials.create',
                                                    [
                                                        $course->id,
                                                        $module->id
                                                    ]
                                                ) }}"
                                                class="px-3 py-2 bg-indigo-600
                                                       text-white rounded-md
                                                       text-xs font-semibold
                                                       hover:bg-indigo-700"
                                            >
                                                + Add Material
                                            </a>

                                        </div>


                                        @if ($module->materials->count() > 0)

                                            <div class="space-y-3">

                                                @foreach (
                                                    $module->materials
                                                    as $material
                                                )

                                                    <div
                                                        class="border
                                                               border-gray-200
                                                               rounded-lg
                                                               p-4"
                                                    >

                                                        <div
                                                            class="flex
                                                                   items-start
                                                                   justify-between
                                                                   gap-4"
                                                        >

                                                            <div>

                                                                <p
                                                                    class="font-semibold
                                                                           text-gray-900"
                                                                >
                                                                    {{ $material->title }}
                                                                </p>


                                                                <p
                                                                    class="text-xs
                                                                           text-gray-500
                                                                           mt-1"
                                                                >
                                                                    Type:
                                                                    {{ ucfirst(
                                                                        str_replace(
                                                                            '_',
                                                                            ' ',
                                                                            $material->type
                                                                        )
                                                                    ) }}
                                                                </p>


                                                                @if (
                                                                    $material->description
                                                                )

                                                                    <p
                                                                        class="text-sm
                                                                               text-gray-600
                                                                               mt-2"
                                                                    >
                                                                        {{ $material->description }}
                                                                    </p>

                                                                @endif

                                                            </div>


                                                            {{-- Material Actions --}}
                                                            <div
                                                                class="flex
                                                                       items-center
                                                                       gap-3"
                                                            >

                                                                @if (
                                                                    $material->external_url
                                                                )

                                                                    <a
                                                                        href="{{ $material->external_url }}"
                                                                        target="_blank"
                                                                        rel="noopener noreferrer"
                                                                        class="text-xs
                                                                               text-indigo-600
                                                                               hover:text-indigo-800"
                                                                    >
                                                                        Open Link
                                                                    </a>

                                                                @elseif (
                                                                    $material->file_path
                                                                )

                                                                    <a
                                                                        href="{{ asset(
                                                                            'storage/' .
                                                                            $material->file_path
                                                                        ) }}"
                                                                        target="_blank"
                                                                        class="text-xs
                                                                               text-green-600
                                                                               hover:text-green-800"
                                                                    >
                                                                        Open File
                                                                    </a>

                                                                @endif


                                                                <a
                                                                    href="{{ route(
                                                                        'trainer.materials.edit',
                                                                        [
                                                                            $course->id,
                                                                            $module->id,
                                                                            $material->id
                                                                        ]
                                                                    ) }}"
                                                                    class="text-xs
                                                                           text-indigo-600
                                                                           hover:text-indigo-800"
                                                                >
                                                                    Edit
                                                                </a>


                                                                <form
                                                                    method="POST"
                                                                    action="{{ route(
                                                                        'trainer.materials.destroy',
                                                                        [
                                                                            $course->id,
                                                                            $module->id,
                                                                            $material->id
                                                                        ]
                                                                    ) }}"
                                                                    onsubmit="return confirm(
                                                                        'Delete this training material?'
                                                                    );"
                                                                >

                                                                    @csrf
                                                                    @method('DELETE')

                                                                    <button
                                                                        type="submit"
                                                                        class="text-xs
                                                                               text-red-600
                                                                               hover:text-red-800"
                                                                    >
                                                                        Delete
                                                                    </button>

                                                                </form>

                                                            </div>

                                                        </div>

                                                    </div>

                                                @endforeach

                                            </div>

                                        @else

                                            <div
                                                class="border border-dashed
                                                       border-gray-300
                                                       rounded-lg p-5
                                                       text-center"
                                            >

                                                <p
                                                    class="text-sm
                                                           text-gray-500"
                                                >
                                                    No training materials
                                                    have been added yet.
                                                </p>

                                                <a
                                                    href="{{ route(
                                                        'trainer.materials.create',
                                                        [
                                                            $course->id,
                                                            $module->id
                                                        ]
                                                    ) }}"
                                                    class="inline-block
                                                           mt-3 text-sm
                                                           text-indigo-600
                                                           hover:text-indigo-800"
                                                >
                                                    Add the first material
                                                </a>

                                            </div>

                                        @endif

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    @else

                        <div
                            class="border border-dashed
                                   border-gray-300 rounded-lg
                                   p-8 text-center"
                        >

                            <h3
                                class="text-lg font-semibold
                                       text-gray-800"
                            >
                                No modules yet
                            </h3>

                            <p
                                class="text-sm text-gray-500
                                       mt-2"
                            >
                                Start building your course by adding
                                your first module.
                            </p>

                            <a
                                href="{{ route(
                                    'trainer.modules.create',
                                    $course->id
                                ) }}"
                                class="inline-block mt-4 px-4 py-2
                                       bg-gray-800 text-white
                                       rounded-md text-sm
                                       font-semibold hover:bg-gray-700"
                            >
                                Add Your First Module
                            </a>

                        </div>

                    @endif

                </div>

            </div>


        </div>

    </div>

</x-app-layout>