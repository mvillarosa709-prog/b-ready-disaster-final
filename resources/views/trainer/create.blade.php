<x-layouts.trainer>
    <x-slot name="title">Upload Assessment Questions CSV — B-READY</x-slot>

    <!-- Header Banner -->
    <div class="bg-white border-b border-slate-200/80 px-4 py-8 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <a href="{{ route('trainer.assessments.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 hover:text-emerald-700 mb-3">
                &larr; Back to Question Bank
            </a>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Upload Assessment Questions via CSV
            </h1>
            <p class="text-slate-500 text-sm mt-1">
                Import questions and choices in bulk using a structured CSV file.
            </p>
        </div>
    </div>

    <!-- Upload Form -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-8 shadow-xs">
            <form action="{{ route('trainer.assessments.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-2">
                        Select CSV File
                    </label>
                    <div class="border-2 border-dashed border-slate-300 rounded-2xl p-8 text-center hover:border-emerald-500 transition bg-slate-50/50">
                        <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="mt-4 flex text-sm leading-6 text-slate-600 justify-center">
                            <label for="assessment_file" class="relative cursor-pointer rounded-md bg-white font-semibold text-emerald-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-emerald-600 hover:text-emerald-500 px-3 py-1 border border-slate-200">
                                <span>Browse file</span>
                                <input id="assessment_file" name="assessment_file" type="file" accept=".csv" required class="sr-only">
                            </label>
                            <p class="pl-2 pt-1 text-xs text-slate-500">or drag and drop CSV</p>
                        </div>
                        <p class="text-xs text-slate-400 mt-2">Only .csv files are supported (Max 5MB)</p>
                    </div>
                </div>

                <!-- CSV Template Guide -->
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 space-y-2">
                    <p class="font-bold text-slate-800">CSV Column Format Requirement:</p>
                    <div class="font-mono bg-white p-2.5 rounded border border-slate-200 text-[11px] overflow-x-auto text-slate-700">
                        Question, Choice A, Choice B, Choice C, Choice D, Correct Answer Text
                    </div>
                    <p class="text-slate-500">The 6th column must match exactly the text of the correct choice.</p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('trainer.assessments.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 shadow-sm transition">
                        Upload & Import Questions
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.trainer>