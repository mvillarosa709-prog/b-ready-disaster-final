<x-app-layout>

    <x-slot name="header">

        <div>

            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create Course') }}
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Create a new training course.
            </p>

        </div>

    </x-slot>


    <div class="py-10">

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg">

                <div class="p-6">

                    {{-- Validation Errors --}}
                    @if ($errors->any())

                        <div class="mb-6 bg-red-100 border border-red-200
                                    text-red-800 px-4 py-3 rounded-lg">

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


                    <form method="POST" action="{{ route('trainer.courses.store') }}">

                        @csrf


                        {{-- Course Title --}}
                        <div class="mb-6">

                            <label
                                for="title"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Course Title
                            </label>

                            <input
                                type="text"
                                id="title"
                                name="title"
                                value="{{ old('title') }}"
                                placeholder="Example: Disaster Preparedness for Teachers"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300
                                       shadow-sm focus:border-indigo-500
                                       focus:ring-indigo-500"
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
                                placeholder="Describe what this course is about."
                                required
                                class="mt-1 block w-full rounded-md border-gray-300
                                       shadow-sm focus:border-indigo-500
                                       focus:ring-indigo-500"
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
                                placeholder="What should participants learn after completing this course?"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300
                                       shadow-sm focus:border-indigo-500
                                       focus:ring-indigo-500"
                            >{{ old('learning_objectives') }}</textarea>

                        </div>


                        {{-- Target Participants --}}
                        <div class="mb-6">

                            <label
                                for="target_participants"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Target Participants
                            </label>

                            <textarea
                                id="target_participants"
                                name="target_participants"
                                rows="3"
                                placeholder="Example: Teachers, school staff, and education personnel"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300
                                       shadow-sm focus:border-indigo-500
                                       focus:ring-indigo-500"
                            >{{ old('target_participants') }}</textarea>

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
                                    placeholder="60"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300
                                           shadow-sm focus:border-indigo-500
                                           focus:ring-indigo-500"
                                >

                                <span class="text-sm text-gray-500">
                                    minutes
                                </span>

                            </div>

                        </div>


                        {{-- Status --}}
                        <div class="mb-8">

                            <label
                                for="status"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Publication Status
                            </label>

                            <select
                                id="status"
                                name="status"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300
                                       shadow-sm focus:border-indigo-500
                                       focus:ring-indigo-500"
                            >

                                <option
                                    value="draft"
                                    {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}
                                >
                                    Draft
                                </option>

                                <option
                                    value="published"
                                    {{ old('status') === 'published' ? 'selected' : '' }}
                                >
                                    Published
                                </option>

                                <option
                                    value="unpublished"
                                    {{ old('status') === 'unpublished' ? 'selected' : '' }}
                                >
                                    Unpublished
                                </option>

                            </select>

                            <p class="text-xs text-gray-500 mt-1">
                                Draft courses are not yet ready for workshop use.
                            </p>

                        </div>


                        {{-- Buttons --}}
                        <div class="flex items-center justify-end gap-3">

                            <a
                                href="{{ route('trainer.courses.index') }}"
                                class="px-4 py-2 border border-gray-300 rounded-md
                                       text-sm font-medium text-gray-700
                                       hover:bg-gray-50"
                            >
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="px-4 py-2 bg-gray-800 border border-transparent
                                       rounded-md font-semibold text-xs text-white
                                       uppercase tracking-widest hover:bg-gray-700
                                       focus:bg-gray-700 active:bg-gray-900
                                       focus:outline-none focus:ring-2
                                       focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                Create Course
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>