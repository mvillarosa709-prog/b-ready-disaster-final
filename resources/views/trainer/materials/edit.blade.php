<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2">
                    <a href="{{ route('trainer.courses.show', $course->id) }}" class="hover:text-emerald-600 transition font-medium truncate max-w-xs">
                        {{ $course->title }}
                    </a>
                    <span>/</span>
                    <span class="text-slate-600 truncate max-w-xs">Module {{ $module->sequence }}</span>
                    <span>/</span>
                    <span class="text-slate-800 font-semibold">Edit Material</span>
                </nav>

                <h2 class="text-2xl font-bold text-slate-900 tracking-tight">
                    {{ __('Edit Training Material') }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Update resource details for <strong>{{ $module->title }}</strong>.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a
                    href="{{ route('trainer.courses.show', $course->id) }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition shadow-sm"
                >
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Course
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50/70 min-h-[calc(100vh-10rem)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

                <!-- Main Form (2 cols) -->
                <div class="lg:col-span-2 space-y-6">

                    @if ($errors->any())
                        <div class="rounded-2xl bg-red-50 border border-red-200 p-4 shadow-xs">
                            <h3 class="text-sm font-bold text-red-900 mb-1">Please fix the following errors:</h3>
                            <ul class="list-disc list-inside text-xs text-red-700 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('trainer.materials.update', [$course->id, $module->id, $material->id]) }}" enctype="multipart/form-data" class="space-y-6" x-data="{ fileName: '' }">
                        @csrf
                        @method('PUT')

                        <!-- Card 1: Material Details -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-7 space-y-6">
                            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-900">Material Details</h3>
                                    <p class="text-xs text-slate-500">Edit material title, type, and summary.</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label for="title" class="block text-sm font-semibold text-slate-800 mb-1.5">
                                        Material Title <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="title"
                                        name="title"
                                        value="{{ old('title', $material->title) }}"
                                        required
                                        class="block w-full rounded-xl border-slate-300 shadow-xs text-slate-900 text-sm focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3.5 transition"
                                    >
                                </div>

                                <div>
                                    <label for="type" class="block text-sm font-semibold text-slate-800 mb-1.5">
                                        Material Type <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        id="type"
                                        name="type"
                                        required
                                        class="block w-full rounded-xl border-slate-300 shadow-xs text-slate-900 text-sm focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3.5 transition"
                                    >
                                        <option value="pdf" {{ old('type', $material->type) === 'pdf' ? 'selected' : '' }}>PDF Document</option>
                                        <option value="presentation" {{ old('type', $material->type) === 'presentation' ? 'selected' : '' }}>Presentation / Slides</option>
                                        <option value="video" {{ old('type', $material->type) === 'video' ? 'selected' : '' }}>Video Lesson</option>
                                        <option value="image" {{ old('type', $material->type) === 'image' ? 'selected' : '' }}>Infographic / Image</option>
                                        <option value="external_resource" {{ old('type', $material->type) === 'external_resource' ? 'selected' : '' }}>External Resource Link</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-semibold text-slate-800 mb-1.5">
                                    Description
                                </label>
                                <textarea
                                    id="description"
                                    name="description"
                                    rows="3"
                                    class="block w-full rounded-xl border-slate-300 shadow-xs text-slate-900 text-sm focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3.5 transition"
                                >{{ old('description', $material->description) }}</textarea>
                            </div>
                        </div>

                        <!-- Card 2: Replace File or URL -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-7 space-y-6">
                            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shadow-xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-900">Resource File &amp; Link</h3>
                                    <p class="text-xs text-slate-500">Leave file empty to keep the existing attachment.</p>
                                </div>
                            </div>

                            @if ($material->file_path || $material->external_url)
                                <div class="p-3.5 rounded-xl bg-emerald-50/70 border border-emerald-200/70 flex items-center justify-between text-xs">
                                    <span class="font-semibold text-emerald-900">Current Attachment:</span>
                                    @if ($material->file_path)
                                        <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" class="font-bold text-emerald-700 hover:underline">
                                            Open Current File &rarr;
                                        </a>
                                    @elseif ($material->external_url)
                                        <a href="{{ $material->external_url }}" target="_blank" rel="noopener noreferrer" class="font-bold text-emerald-700 hover:underline">
                                            Open Current Link &rarr;
                                        </a>
                                    @endif
                                </div>
                            @endif

                            <div>
                                <label for="file" class="block text-sm font-semibold text-slate-800 mb-2">Replace File</label>
                                <label for="file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-slate-300 rounded-2xl cursor-pointer bg-slate-50/70 hover:bg-emerald-50/40 hover:border-emerald-400 transition px-4 text-center">
                                    <p class="text-sm font-semibold text-slate-700" x-show="!fileName">
                                        Click to upload a replacement file (optional)
                                    </p>
                                    <p class="text-sm font-bold text-emerald-700" x-show="fileName" x-text="'Selected: ' + fileName"></p>
                                    <p class="text-xs text-slate-400 mt-1">Leave empty to keep existing file</p>
                                    <input type="file" id="file" name="file" class="hidden" @change="fileName = $event.target.files[0]?.name || ''">
                                </label>
                            </div>

                            <div>
                                <label for="external_url" class="block text-sm font-semibold text-slate-800 mb-1.5">External URL</label>
                                <input
                                    type="url"
                                    id="external_url"
                                    name="external_url"
                                    value="{{ old('external_url', $material->external_url) }}"
                                    placeholder="https://example.com/resource"
                                    class="block w-full rounded-xl border-slate-300 shadow-xs text-slate-900 text-sm focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3.5 transition"
                                >
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-4 pt-2">
                            <a href="{{ route('trainer.courses.show', $course->id) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition shadow-xs">
                                Cancel &amp; Return
                            </a>
                            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 shadow-sm transition">
                                Save Changes
                            </button>
                        </div>
                    </form>

                </div>

                <!-- Right Sidebar -->
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-700">Module Context</h4>
                        <div>
                            <span class="text-xs text-slate-400">Course</span>
                            <p class="text-sm font-bold text-slate-900">{{ $course->title }}</p>
                        </div>
                        <div class="pt-2 border-t border-slate-100">
                            <span class="text-xs text-slate-400">Module {{ $module->sequence }}</span>
                            <p class="text-sm font-bold text-slate-900">{{ $module->title }}</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
