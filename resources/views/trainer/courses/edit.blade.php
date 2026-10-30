<x-app-layout>

    <x-slot name="header">

        <div>

            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Course') }}
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Update your course information.
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


                    <form
                        method="POST"
                        action="{{ route('trainer.courses.update', $course->id) }}"
                    >

                        @csrf
                        @method('PUT')


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
                                value="{{ old('title', $course->title) }}"
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
                                required
                                class="mt-1 block w-full rounded-md border-gray-300
                                       shadow-sm focus:border-indigo-500
                                       focus:ring-indigo-500"
                            >{{ old('description', $course->description) }}</textarea>

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
                                class="mt-1 block w-full rounded-md border-gray-300
                                       shadow-sm focus:border-indigo-500
                                       focus:ring-indigo-500"
                            >{{ old('learning_objectives', $course->learning_objectives) }}</textarea>

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
                                required
                                class="mt-1 block w-full rounded-md border-gray-300
                                       shadow-sm focus:border-indigo-500
                                       focus:ring-indigo-500"
                            >{{ old('target_participants', $course->target_participants) }}</textarea>

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
                                    value="{{ old('estimated_duration', $course->estimated_duration) }}"
                                    min="1"
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
                                    {{ old('status', $course->status) === 'draft' ? 'selected' : '' }}
                                >
                                    Draft
                                </option>

                                <option
                                    value="published"
                                    {{ old('status', $course->status) === 'published' ? 'selected' : '' }}
                                >
                                    Published
                                </option>

                                <option
                                    value="unpublished"
                                    {{ old('status', $course->status) === 'unpublished' ? 'selected' : '' }}
                                >
                                    Unpublished
                                </option>

                            </select>

                        </div>


                        {{-- Buttons --}}
                        <div class="flex items-center justify-end gap-3">

                            <a
                                href="{{ route('trainer.courses.show', $course->id) }}"
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
                                Save Changes
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>