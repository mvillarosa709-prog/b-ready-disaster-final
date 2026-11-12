<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2">
                    <a href="{{ route('trainer.courses.show', $course->id) }}" class="hover:text-emerald-600 transition font-medium truncate max-w-xs">
                        {{ $course->title }}
                    </a>
                    <span>/</span>
                    <span class="text-slate-600">Module {{ $module->sequence }}</span>
                    <span>/</span>
                    <span class="text-slate-800 font-semibold">Quiz</span>
                </nav>

                <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-bold text-slate-900 tracking-tight">
                        Module Quiz
                    </h2>
                    @if ($quiz->is_published)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            Published
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                            Draft Mode
                        </span>
                    @endif
                </div>
                <p class="text-sm text-slate-500 mt-1">
                    {{ $course->title }} &bull; Module {{ $module->sequence }}: <strong>{{ $module->title }}</strong>
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
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                <div class="lg:col-span-2 space-y-6">

            @if ($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-900 shadow-sm">
                    <p class="font-bold text-sm">Please fix the following:</p>
                    <ul class="mt-2 list-inside list-disc text-xs text-red-700 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-900 text-sm font-medium shadow-sm flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <section class="space-y-4 rounded-2xl border border-emerald-200/80 bg-emerald-50/40 p-6 shadow-sm">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Import a complete quiz</h3>
                    <p class="mt-1 text-xs text-slate-600">
                        Use the formatted Excel template to prepare multiple-choice, true/false, and fill-in-the-blank questions. Importing replaces the current question list and saves the quiz as a draft for review.
                    </p>
                </div>

                <div class="flex flex-wrap items-end gap-3">
                    <a href="{{ route('trainer.modules.quiz.template', [$course->id, $module->id]) }}"
                        class="inline-flex items-center rounded-xl border border-emerald-300 bg-white px-4 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-50 transition shadow-sm">
                        Download Excel template
                    </a>

                    <form method="POST" enctype="multipart/form-data"
                        action="{{ route('trainer.modules.quiz.import', [$course->id, $module->id]) }}"
                        class="flex flex-wrap items-end gap-3">
                        @csrf
                        <div>
                            <label for="quiz_file" class="block text-xs font-semibold text-slate-700">Quiz file (.csv or .xlsx, max 5 MB)</label>
                            <input id="quiz_file" type="file" name="quiz_file" accept=".csv,.xlsx,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required
                                class="mt-1 block w-full text-xs text-slate-600 file:mr-3 file:rounded-xl file:border file:border-slate-200 file:bg-white file:px-3.5 file:py-2 file:text-xs file:font-semibold file:text-emerald-700 hover:file:bg-emerald-50">
                        </div>
                        <button type="submit"
                            onclick="return confirm('Importing this file replaces all current quiz questions. Continue?')"
                            class="rounded-xl bg-emerald-600 px-4 py-2 text-xs font-semibold text-white hover:bg-emerald-700 transition shadow-sm">
                            Upload quiz
                        </button>
                    </form>
                </div>

                <p class="text-xs text-slate-500">
                    The Excel workbook includes instructions and examples for each question type. You can also import a CSV with the documented headers.
                </p>
            </section>

            <!-- Manual Quiz Form -->
            <form method="POST" action="{{ route('trainer.modules.quiz.update', [$course->id, $module->id]) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Quiz Settings Card -->
                <section class="space-y-5 rounded-2xl border border-slate-200/80 bg-white p-6 sm:p-7 shadow-xs">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shadow-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Quiz Settings</h3>
                            <p class="text-xs text-slate-500">The quiz appears inline after this module's materials.</p>
                        </div>
                    </div>

                    <div>
                        <label for="title" class="block text-sm font-semibold text-slate-800 mb-1.5">Quiz title <span class="text-red-500">*</span></label>
                        <input id="title" name="title" value="{{ old('title', $quiz->title) }}" required maxlength="255"
                            class="block w-full rounded-xl border-slate-300 shadow-xs text-sm focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3.5">
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-semibold text-slate-800 mb-1.5">Instructions</label>
                        <textarea id="description" name="description" rows="2"
                            class="block w-full rounded-xl border-slate-300 shadow-xs text-sm focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3.5">{{ old('description', $quiz->description) }}</textarea>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-3">
                        <div>
                            <label for="passing_score" class="block text-sm font-semibold text-slate-800 mb-1.5">Passing score (%)</label>
                            <input id="passing_score" type="number" name="passing_score" min="1" max="100" required
                                value="{{ old('passing_score', $quiz->passing_score) }}"
                                class="block w-full rounded-xl border-slate-300 shadow-xs text-sm focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3.5">
                        </div>
                        <div>
                            <label for="time_limit" class="block text-sm font-semibold text-slate-800 mb-1.5">Time limit (minutes)</label>
                            <input id="time_limit" type="number" name="time_limit" min="1" required
                                value="{{ old('time_limit', $quiz->time_limit) }}"
                                class="block w-full rounded-xl border-slate-300 shadow-xs text-sm focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3.5">
                        </div>
                        <div>
                            <label for="max_attempts" class="block text-sm font-semibold text-slate-800 mb-1.5">Maximum attempts</label>
                            <input id="max_attempts" type="number" name="max_attempts" min="1" required
                                value="{{ old('max_attempts', $quiz->max_attempts) }}"
                                class="block w-full rounded-xl border-slate-300 shadow-xs text-sm focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3.5">
                        </div>
                    </div>
                </section>

                <!-- Quiz Questions Builder -->
                <section class="space-y-5 rounded-2xl border border-slate-200/80 bg-white p-6 sm:p-7 shadow-xs">
                    <div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shadow-xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900">Quiz questions</h3>
                                <p class="text-xs text-slate-500">Choose multiple choice, true/false, or fill in the blank for each question.</p>
                            </div>
                        </div>
                        <button type="button" id="add-question" class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-100 transition">
                            + Add question
                        </button>
                    </div>

                    <div id="questions" class="space-y-5">
                        @foreach ($questionData as $questionIndex => $question)
                            <fieldset class="question-card space-y-4 rounded-2xl border border-slate-200 bg-slate-50/40 p-5">
                                <div class="flex items-center justify-between gap-3">
                                    <legend class="font-bold text-sm text-slate-900">Question {{ $loop->iteration }}</legend>
                                    <button type="button" class="remove-question text-xs font-semibold text-rose-600 hover:text-rose-800">Remove</button>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Question text</label>
                                    <textarea name="questions[{{ $questionIndex }}][question_text]" rows="2" required
                                        class="block w-full rounded-xl border-slate-300 bg-white shadow-xs text-sm focus:border-emerald-500 focus:ring-emerald-500/20">{{ $question['question_text'] ?? '' }}</textarea>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Question type</label>
                                        <select name="questions[{{ $questionIndex }}][type]" class="question-type block w-full rounded-xl border-slate-300 bg-white shadow-xs text-sm focus:border-emerald-500 focus:ring-emerald-500/20">
                                            <option value="multiple_choice" @selected(($question['type'] ?? 'multiple_choice') === 'multiple_choice')>Multiple choice</option>
                                            <option value="true_false" @selected(($question['type'] ?? '') === 'true_false')>True / False</option>
                                            <option value="fill_blank" @selected(($question['type'] ?? '') === 'fill_blank')>Fill in the blank</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Points</label>
                                        <input type="number" name="questions[{{ $questionIndex }}][points]" min="1" required value="{{ $question['points'] ?? 1 }}"
                                            class="block w-full rounded-xl border-slate-300 bg-white shadow-xs text-sm focus:border-emerald-500 focus:ring-emerald-500/20">
                                    </div>
                                </div>

                                <div data-question-type="multiple_choice" class="question-type-fields grid gap-3 sm:grid-cols-2">
                                    @foreach (range(0, 3) as $choiceIndex)
                                        <div class="flex items-start gap-2 bg-white p-2.5 rounded-xl border border-slate-200/80">
                                            <input type="radio" name="questions[{{ $questionIndex }}][correct_choice]" value="{{ $choiceIndex }}" required
                                                {{ (int) ($question['correct_choice'] ?? 0) === $choiceIndex ? 'checked' : '' }}
                                                aria-label="Mark option {{ $choiceIndex + 1 }} as correct"
                                                class="mt-2.5 border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                            <div class="flex-1">
                                                <label class="block text-[11px] font-semibold text-slate-500">Option {{ $choiceIndex + 1 }}</label>
                                                <input type="text" name="questions[{{ $questionIndex }}][choices][{{ $choiceIndex }}][text]" required maxlength="1000"
                                                    value="{{ $question['choices'][$choiceIndex]['text'] ?? '' }}"
                                                    class="mt-1 block w-full rounded-lg border-slate-200 shadow-2xs text-xs focus:border-emerald-500 focus:ring-emerald-500/20">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div data-question-type="true_false" class="question-type-fields">
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Correct answer</label>
                                    <select name="questions[{{ $questionIndex }}][true_false_answer]" class="block w-full max-w-xs rounded-xl border-slate-300 bg-white shadow-xs text-sm focus:border-emerald-500">
                                        <option value="True" @selected(($question['true_false_answer'] ?? 'True') === 'True')>True</option>
                                        <option value="False" @selected(($question['true_false_answer'] ?? 'True') === 'False')>False</option>
                                    </select>
                                </div>

                                <div data-question-type="fill_blank" class="question-type-fields">
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Accepted answers</label>
                                    <textarea name="questions[{{ $questionIndex }}][accepted_answers]" rows="3" placeholder="One accepted answer per line"
                                        class="block w-full rounded-xl border-slate-300 bg-white shadow-xs text-sm focus:border-emerald-500">{{ $question['accepted_answers'] ?? '' }}</textarea>
                                    <p class="mt-1 text-xs text-slate-500">Learner answers ignore case and extra spaces. Add alternate correct spellings on separate lines.</p>
                                </div>
                            </fieldset>
                        @endforeach
                    </div>
                </section>

                <section class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-slate-200/80 bg-white p-6 shadow-xs">
                    <label class="flex items-center gap-2.5 text-sm font-semibold text-slate-800 cursor-pointer">
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published', $quiz->is_published) ? 'checked' : '' }}
                            class="rounded border-slate-300 text-emerald-600 shadow-xs focus:ring-emerald-500">
                        Publish quiz (required before publishing the course)
                    </label>

                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('trainer.courses.show', $course->id) }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">Cancel</a>
                        <button type="submit" class="rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 shadow-sm shadow-emerald-600/30 transition">Save quiz</button>
                    </div>
                </section>
            </form>

            <template id="question-template">
                <fieldset class="question-card space-y-4 rounded-2xl border border-slate-200 bg-slate-50/40 p-5">
                    <div class="flex items-center justify-between gap-3">
                        <legend class="font-bold text-sm text-slate-900">Question</legend>
                        <button type="button" class="remove-question text-xs font-semibold text-rose-600 hover:text-rose-800">Remove</button>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Question text</label>
                        <textarea name="questions[__QUESTION__][question_text]" rows="2" required class="block w-full rounded-xl border-slate-300 bg-white shadow-xs text-sm focus:border-emerald-500 focus:ring-emerald-500/20"></textarea>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Question type</label>
                            <select name="questions[__QUESTION__][type]" class="question-type block w-full rounded-xl border-slate-300 bg-white shadow-xs text-sm focus:border-emerald-500 focus:ring-emerald-500/20">
                                <option value="multiple_choice">Multiple choice</option>
                                <option value="true_false">True / False</option>
                                <option value="fill_blank">Fill in the blank</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Points</label>
                            <input type="number" name="questions[__QUESTION__][points]" min="1" required value="1" class="block w-full rounded-xl border-slate-300 bg-white shadow-xs text-sm focus:border-emerald-500 focus:ring-emerald-500/20">
                        </div>
                    </div>
                    <div data-question-type="multiple_choice" class="question-type-fields grid gap-3 sm:grid-cols-2">
                        @foreach (range(0, 3) as $choiceIndex)
                            <div class="flex items-start gap-2 bg-white p-2.5 rounded-xl border border-slate-200/80">
                                <input type="radio" name="questions[__QUESTION__][correct_choice]" value="{{ $choiceIndex }}" required aria-label="Mark option {{ $choiceIndex + 1 }} as correct" class="mt-2.5 border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <div class="flex-1">
                                    <label class="block text-[11px] font-semibold text-slate-500">Option {{ $choiceIndex + 1 }}</label>
                                    <input type="text" name="questions[__QUESTION__][choices][{{ $choiceIndex }}][text]" required maxlength="1000" class="mt-1 block w-full rounded-lg border-slate-200 shadow-2xs text-xs focus:border-emerald-500 focus:ring-emerald-500/20">
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div data-question-type="true_false" class="question-type-fields hidden">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Correct answer</label>
                        <select name="questions[__QUESTION__][true_false_answer]" disabled class="block w-full max-w-xs rounded-xl border-slate-300 bg-white shadow-xs text-sm focus:border-emerald-500">
                            <option value="True">True</option>
                            <option value="False">False</option>
                        </select>
                    </div>
                    <div data-question-type="fill_blank" class="question-type-fields hidden">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Accepted answers</label>
                        <textarea name="questions[__QUESTION__][accepted_answers]" rows="3" disabled placeholder="One accepted answer per line" class="block w-full rounded-xl border-slate-300 bg-white shadow-xs text-sm focus:border-emerald-500"></textarea>
                        <p class="mt-1 text-xs text-slate-500">Add alternate correct answers on separate lines.</p>
                    </div>
                </fieldset>
            </template>
                </div>

                <!-- Right Sidebar: Course Creation Steps -->
                <div class="space-y-6 lg:sticky lg:top-6">
                    @include('trainer.courses._creation-steps', ['activeStep' => 4, 'course' => $course])
                </div>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const questions = document.getElementById('questions');
            const template = document.getElementById('question-template');
            let questionIndex = {{ count($questionData) }};

            const updateQuestionType = (card) => {
                const selectedType = card.querySelector('.question-type').value;
                card.querySelectorAll('.question-type-fields').forEach((section) => {
                    const active = section.dataset.questionType === selectedType;
                    section.classList.toggle('hidden', !active);
                    section.querySelectorAll('input, select, textarea').forEach((field) => {
                        field.disabled = !active;
                        if (field.type === 'radio' || field.name.endsWith('[text]')) {
                            field.required = active;
                        }
                    });
                });
            };

            questions.querySelectorAll('.question-card').forEach(updateQuestionType);
            questions.addEventListener('change', (event) => {
                if (event.target.matches('.question-type')) {
                    updateQuestionType(event.target.closest('.question-card'));
                }
            });

            document.getElementById('add-question').addEventListener('click', () => {
                const html = template.innerHTML.replaceAll('__QUESTION__', String(questionIndex++));
                questions.insertAdjacentHTML('beforeend', html);
                updateQuestionType(questions.lastElementChild);
            });

            questions.addEventListener('click', (event) => {
                if (event.target.closest('.remove-question')) {
                    event.target.closest('.question-card').remove();
                }
            });
        })();
    </script>
</x-app-layout>
