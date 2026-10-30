<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Add Module') }}
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Add a new module to {{ $course->title }}.
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg">

                <div class="p-6">

                    {{-- Validation Errors --}}
                    @if ($errors->any())
                        <div class="mb-6 bg-red-100 border border-red-200 text-red-800 px-4 py-3 rounded-lg">

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


                    {{-- Module Form --}}
                    <form
                        method="POST"
                        action="{{ route('trainer.modules.store', $course->id) }}"
                    >

                        @csrf


                        {{-- Module Title --}}
                        <div class="mb-6">

                            <label
                                for="title"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Module Title
                            </label>

                            <input
                                type="text"
                                id="title"
                                name="title"
                                value="{{ old('title') }}"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Example: Understanding Disaster Risks"
                            >

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
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Describe what this module covers."
                            >{{ old('description') }}</textarea>

                        </div>


                        {{-- Learning Objectives --}}
                        <div class="mb-6">

                            <label
                                for="learning_objectives"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Learning Objectives
                            </label>

                            <textarea
                                id="learning_objectives"
                                name="learning_objectives"
                                rows="4"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="What should participants learn from this module?"
                            >{{ old('learning_objectives') }}</textarea>

                        </div>


                        {{-- Estimated Duration --}}
                        <div class="mb-6">

                            <label
                                for="estimated_duration"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Estimated Duration
                            </label>

                            <div class="flex items-center gap-3">

                                <input
                                    type="number"
                                    id="estimated_duration"
                                    name="estimated_duration"
                                    value="{{ old('estimated_duration') }}"
                                    min="1"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="30"
                                >

                                <span class="text-sm text-gray-500">
                                    minutes
                                </span>

                            </div>

                        </div>


                        {{-- Module Order --}}
                        <div class="mb-6">

                            <label
                                for="sequence"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Module Order
                            </label>

                            <div class="flex items-center gap-3">

                                <input
                                    type="number"
                                    id="sequence"
                                    name="sequence"
                                    value="{{ old('sequence', 1) }}"
                                    min="1"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                <span class="text-sm text-gray-500">
                                    order
                                </span>

                            </div>

                            <p class="text-xs text-gray-500 mt-1">
                                Example: 1 for the first module, 2 for the second module.
                            </p>

                        </div>


                        {{-- Required Module --}}
                        <div class="mb-8">

                            <label class="flex items-center">

                                <input
                                    type="checkbox"
                                    name="is_required"
                                    value="1"
                                    {{ old('is_required', true) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                >

                                <span class="ml-2 text-sm text-gray-700">
                                    This module is required
                                </span>

                            </label>

                        </div>


                        {{-- Buttons --}}
                        <div class="flex items-center justify-end gap-3">

                            <a
                                href="{{ route('trainer.courses.show', $course->id) }}"
                                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                            >
                                Create Module
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>