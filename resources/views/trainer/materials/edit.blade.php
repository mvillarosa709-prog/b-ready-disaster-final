<x-app-layout>

    <x-slot name="header">

        <div>

            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Training Material') }}
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Update the training material for {{ $module->title }}.
            </p>

        </div>

    </x-slot>


    <div class="py-10">

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg">

                <div class="p-6">


                    {{-- Validation Errors --}}
                    @if ($errors->any())

                        <div
                            class="mb-6 bg-red-100 border border-red-200
                                   text-red-800 px-4 py-3 rounded-lg"
                        >

                            <p class="font-semibold mb-2">
                                Please correct the following errors:
                            </p>

                            <ul class="list-disc list-inside text-sm">

                                @foreach ($errors->all() as $error)

                                    <li>{{ $error }}</li>

                                @endforeach

                            </ul>

                        </div>

                    @endif


                    {{-- Module Information --}}
                    <div class="mb-6 bg-gray-50 rounded-lg p-4">

                        <p class="text-sm text-gray-500">
                            Course
                        </p>

                        <p class="font-semibold text-gray-800">
                            {{ $course->title }}
                        </p>

                        <p class="text-sm text-gray-500 mt-3">
                            Module
                        </p>

                        <p class="font-semibold text-gray-800">
                            {{ $module->title }}
                        </p>

                    </div>


                    {{-- Current Material --}}
                    <div class="mb-6 border rounded-lg p-4">

                        <p class="text-sm font-semibold text-gray-700">
                            Current Material
                        </p>

                        <p class="text-sm text-gray-800 mt-2">
                            {{ $material->title }}
                        </p>


                        @if ($material->file_path)

                            <a
                                href="{{ asset(
                                    'storage/' . $material->file_path
                                ) }}"
                                target="_blank"
                                class="inline-block mt-2 text-sm
                                       text-indigo-600 hover:text-indigo-800"
                            >
                                Open Current File
                            </a>

                        @elseif ($material->external_url)

                            <a
                                href="{{ $material->external_url }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-block mt-2 text-sm
                                       text-indigo-600 hover:text-indigo-800"
                            >
                                Open Current Link
                            </a>

                        @else

                            <p class="text-xs text-gray-500 mt-2">
                                No file or external link attached.
                            </p>

                        @endif

                    </div>


                    {{-- Edit Form --}}
                    <form
                        method="POST"
                        action="{{ route(
                            'trainer.materials.update',
                            [
                                $course->id,
                                $module->id,
                                $material->id
                            ]
                        ) }}"
                        enctype="multipart/form-data"
                    >

                        @csrf

                        @method('PUT')


                        {{-- Title --}}
                        <div class="mb-6">

                            <label
                                for="title"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Material Title
                            </label>

                            <input
                                type="text"
                                id="title"
                                name="title"
                                value="{{ old(
                                    'title',
                                    $material->title
                                ) }}"
                                required
                                class="mt-1 block w-full rounded-md
                                       border-gray-300 shadow-sm
                                       focus:border-indigo-500
                                       focus:ring-indigo-500"
                            >

                        </div>


                        {{-- Type --}}
                        <div class="mb-6">

                            <label
                                for="type"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Material Type
                            </label>

                            <select
                                id="type"
                                name="type"
                                required
                                class="mt-1 block w-full rounded-md
                                       border-gray-300 shadow-sm
                                       focus:border-indigo-500
                                       focus:ring-indigo-500"
                            >

                                <option value="pdf"
                                    {{ old('type', $material->type) === 'pdf'
                                        ? 'selected'
                                        : '' }}>
                                    PDF
                                </option>

                                <option value="presentation"
                                    {{ old('type', $material->type) === 'presentation'
                                        ? 'selected'
                                        : '' }}>
                                    Presentation
                                </option>

                                <option value="video"
                                    {{ old('type', $material->type) === 'video'
                                        ? 'selected'
                                        : '' }}>
                                    Video
                                </option>

                                <option value="image"
                                    {{ old('type', $material->type) === 'image'
                                        ? 'selected'
                                        : '' }}>
                                    Image
                                </option>

                                <option value="external_resource"
                                    {{ old('type', $material->type) === 'external_resource'
                                        ? 'selected'
                                        : '' }}>
                                    External Resource
                                </option>

                            </select>

                        </div>


                        {{-- Description --}}
                        <div class="mb-6">

                            <label
                                for="description"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Description
                            </label>

                            <textarea
                                id="description"
                                name="description"
                                rows="4"
                                class="mt-1 block w-full rounded-md
                                       border-gray-300 shadow-sm
                                       focus:border-indigo-500
                                       focus:ring-indigo-500"
                            >{{ old(
                                'description',
                                $material->description
                            ) }}</textarea>

                        </div>


                        {{-- Replace File --}}
                        <div class="mb-6">

                            <label
                                for="file"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Replace File
                            </label>

                            <input
                                type="file"
                                id="file"
                                name="file"
                                class="mt-1 block w-full text-sm text-gray-700"
                            >

                            <p class="text-xs text-gray-500 mt-1">
                                Leave empty to keep the current file.
                            </p>

                        </div>


                        {{-- External URL --}}
                        <div class="mb-8">

                            <label
                                for="external_url"
                                class="block text-sm font-medium text-gray-700"
                            >
                                External URL
                            </label>

                            <input
                                type="url"
                                id="external_url"
                                name="external_url"
                                value="{{ old(
                                    'external_url',
                                    $material->external_url
                                ) }}"
                                class="mt-1 block w-full rounded-md
                                       border-gray-300 shadow-sm
                                       focus:border-indigo-500
                                       focus:ring-indigo-500"
                                placeholder="https://example.com/resource"
                            >

                        </div>


                        {{-- Buttons --}}
                        <div class="flex items-center justify-end gap-3">

                            <a
                                href="{{ route(
                                    'trainer.courses.show',
                                    $course->id
                                ) }}"
                                class="px-4 py-2 border border-gray-300
                                       rounded-md text-sm font-medium
                                       text-gray-700 hover:bg-gray-50"
                            >
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="px-4 py-2 bg-gray-800
                                       border border-transparent
                                       rounded-md font-semibold text-xs
                                       text-white uppercase tracking-widest
                                       hover:bg-gray-700"
                            >
                                Save Changes
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>