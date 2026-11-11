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
                    <span class="text-slate-800 font-semibold">Add Material</span>
                </nav>

                <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-bold text-slate-900 tracking-tight">
                        {{ __('Add Training Material') }}
                    </h2>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                        Module Resource
                    </span>
                </div>
                <p class="text-sm text-slate-500 mt-1">
                    Attach guides, slides, videos, or reference links to <strong>{{ $module->title }}</strong>.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a
                    href="{{ route('trainer.courses.show', $course->id) }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-400 transition shadow-sm"
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
                            <div class="flex items-start gap-3">
                                <div class="shrink-0 p-1 bg-red-100 rounded-lg text-red-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-sm font-bold text-red-900">Please fix the following errors:</h3>
                                    <ul class="mt-2 list-disc list-inside text-xs text-red-700 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('trainer.materials.store', [$course->id, $module->id]) }}" enctype="multipart/form-data" class="space-y-6" x-data="{ fileName: '' }">
                        @csrf

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
                                    <p class="text-xs text-slate-500">Title, format type, and summary notes for learners.</p>
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
                                        value="{{ old('title') }}"
                                        required
                                        placeholder="e.g. Disaster Preparedness Field Guide"
                                        class="block w-full rounded-xl border-slate-300 shadow-xs text-slate-900 text-sm focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3.5 placeholder:text-slate-400 transition"
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
                                        <option value="">Select material type</option>
                                        <option value="pdf" {{ old('type') === 'pdf' ? 'selected' : '' }}>PDF Document</option>
                                        <option value="presentation" {{ old('type') === 'presentation' ? 'selected' : '' }}>Presentation / Slides</option>
                                        <option value="video" {{ old('type') === 'video' ? 'selected' : '' }}>Video Lesson</option>
                                        <option value="image" {{ old('type') === 'image' ? 'selected' : '' }}>Infographic / Image</option>
                                        <option value="external_resource" {{ old('type') === 'external_resource' ? 'selected' : '' }}>External Resource Link</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-semibold text-slate-800 mb-1.5">
                                    Description <span class="text-xs font-normal text-slate-400">(optional)</span>
                                </label>
                                <textarea
                                    id="description"
                                    name="description"
                                    rows="3"
                                    placeholder="Briefly explain what teachers should read or review in this resource."
                                    class="block w-full rounded-xl border-slate-300 shadow-xs text-slate-900 text-sm focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3.5 placeholder:text-slate-400 transition"
                                >{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <!-- Card 2: File Upload or Link -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-7 space-y-6">
                            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shadow-xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-900">Resource File or External Link</h3>
                                    <p class="text-xs text-slate-500">Upload a document directly or provide a web link.</p>
                                </div>
                            </div>

                            <!-- Styled File Upload Box -->
                            <div>
                                <label for="file" class="block text-sm font-semibold text-slate-800 mb-2">
                                    Upload File
                                </label>
                                <label for="file" class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-slate-300 rounded-2xl cursor-pointer bg-slate-50/70 hover:bg-emerald-50/40 hover:border-emerald-400 transition px-4 text-center">
                                    <svg class="w-8 h-8 text-emerald-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="text-sm font-semibold text-slate-700" x-show="!fileName">
                                        Click to choose a file from your computer
                                    </p>
                                    <p class="text-sm font-bold text-emerald-700" x-show="fileName" x-text="'Selected: ' + fileName"></p>
                                    <p class="text-xs text-slate-400 mt-1">PDF, PPTX, MP4, PNG, JPG &bull; Max 50 MB</p>
                                    <input
                                        type="file"
                                        id="file"
                                        name="file"
                                        class="hidden"
                                        @change="fileName = $event.target.files[0]?.name || ''"
                                    >
                                </label>
                            </div>

                            <div class="relative flex py-1 items-center">
                                <div class="flex-grow border-t border-slate-200"></div>
                                <span class="flex-shrink mx-4 text-xs font-semibold text-slate-400 uppercase">Or link external URL</span>
                                <div class="flex-grow border-t border-slate-200"></div>
                            </div>

                            <div>
                                <label for="external_url" class="block text-sm font-semibold text-slate-800 mb-1.5">
                                    External URL
                                </label>
                                <input
                                    type="url"
                                    id="external_url"
                                    name="external_url"
                                    value="{{ old('external_url') }}"
                                    placeholder="https://www.youtube.com/watch?v=... or Google Drive link"
                                    class="block w-full rounded-xl border-slate-300 shadow-xs text-slate-900 text-sm focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3.5 placeholder:text-slate-400 transition"
                                >
                                <p class="mt-1.5 text-xs text-slate-500">
                                    Great for YouTube training videos, Google Drive folders, or DepEd DRRM portal references.
                                </p>
                            </div>
                        </div>

                        <!-- Bottom Actions -->
                        <div class="flex items-center justify-between gap-4 pt-2">
                            <a
                                href="{{ route('trainer.courses.show', $course->id) }}"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition shadow-xs"
                            >
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Cancel &amp; Return
                            </a>

                            <button
                                type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 shadow-sm shadow-emerald-600/30 transition transform active:scale-95"
                            >
                                <span>Add Training Material</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>
                    </form>

                </div>

                <!-- Right Sidebar -->
                <div class="space-y-6">
                    @include('trainer.courses._creation-steps', ['activeStep' => 3, 'course' => $course])

                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-700 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Target Module
                        </h4>
                        <div>
                            <span class="text-xs text-slate-400">Course</span>
                            <p class="text-sm font-bold text-slate-900">{{ $course->title }}</p>
                        </div>
                        <div class="pt-2 border-t border-slate-100">
                            <span class="text-xs text-slate-400">Module {{ $module->sequence }}</span>
                            <p class="text-sm font-bold text-slate-900">{{ $module->title }}</p>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-emerald-100 bg-gradient-to-br from-emerald-50/80 to-teal-50/40 p-5">
                        <div class="flex items-start gap-3">
                            <span class="text-2xl">📂</span>
                            <div>
                                <h4 class="text-sm font-bold text-emerald-950">Material Guidelines</h4>
                                <ul class="mt-2 text-xs text-emerald-900 space-y-2">
                                    <li class="flex items-start gap-1.5">
                                        <span class="text-emerald-600 font-bold">•</span>
                                        <span>Provide either a downloadable file or an external resource URL.</span>
                                    </li>
                                    <li class="flex items-start gap-1.5">
                                        <span class="text-emerald-600 font-bold">•</span>
                                        <span>PDF handouts and checklists work best for offline school drills.</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
