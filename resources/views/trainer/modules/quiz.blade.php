<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Module Quiz</h2>
            <p class="mt-1 text-sm text-gray-500">{{ $course->title }} &bull; Module {{ $module->sequence }}: {{ $module->title }}</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl space-y-6 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                    <p class="font-semibold">Please fix the following:</p>
                    <ul class="mt-2 list-inside list-disc text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            <section class="space-y-4 rounded-xl border border-indigo-200 bg-indigo-50/60 p-6">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900">Import a complete quiz</h3>
                    <p class="mt-1 text-sm text-slate-600">
                        Use the formatted Excel template to prepare multiple-choice, true/false, and fill-in-the-blank questions. Importing replaces the current question list and saves the quiz as a draft for review.
                    </p>
                </div>

                <div class="flex flex-wrap items-end gap-3">
                    <a href="{{ route('trainer.modules.quiz.template', [$course->id, $module->id]) }}"
                        class="inline-flex items-center rounded-md border border-indigo-300 bg-white px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-50">
                        Download Excel template
                    </a>

                    <form method="POST" enctype="multipart/form-data"
                        action="{{ route('trainer.modules.quiz.import', [$course->id, $module->id]) }}"
                        class="flex flex-wrap items-end gap-3">
                        @csrf
                        <div>
                            <label for="quiz_file" class="block text-xs font-medium text-slate-700">Quiz file (.csv or .xlsx, max 5 MB)</label>
                            <input id="quiz_file" type="file" name="quiz_file" accept=".csv,.xlsx,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required
                                class="mt-1 block w-full text-sm text-slate-600 file:mr-3 file:rounded-md file:border-0 file:bg-white file:px-3 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>
                        <button type="submit"
                            onclick="return confirm('Importing this file replaces all current quiz questions. Continue?')"
                            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                            Upload quiz
                        </button>
                    </form>
                </div>

                <p class="text-xs text-slate-500">
                    The Excel workbook includes instructions and examples for each question type. You can also import a CSV with the documented headers.
                </p>
            </section>

            <form method="POST" action="{{ route('trainer.modules.quiz.update', [$course->id, $module->id]) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <section class="space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Quiz settings</h3>
                        <p class="mt-1 text-sm text-slate-500">The quiz appears inline after this module's materials.</p>
                    </div>

                    <div>
                        <label for="title" class="block text-sm font-medium text-slate-700">Quiz title</label>
                        <input id="title" name="title" value="{{ old('title', $quiz->title) }}" required maxlength="255"
                            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700">Instructions</label>
                        <textarea id="description" name="description" rows="3"
                            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $quiz->description) }}</textarea>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-3">
                        <div>
                            <label for="passing_score" class="block text-sm font-medium text-slate-700">Passing score (%)</label>
                            <input id="passing_score" type="number" name="passing_score" min="1" max="100" required
                                value="{{ old('passing_score', $quiz->passing_score) }}"
                                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="time_limit" class="block text-sm font-medium text-slate-700">Time limit (minutes)</label>
                            <input id="time_limit" type="number" name="time_limit" min="1" required
                                value="{{ old('time_limit', $quiz->time_limit) }}"
                                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="max_attempts" class="block text-sm font-medium text-slate-700">Maximum attempts</label>
                            <input id="max_attempts" type="number" name="max_attempts" min="1" required
                                value="{{ old('max_attempts', $quiz->max_attempts) }}"
                                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </section>

                <section class="space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Quiz questions</h3>
                            <p class="mt-1 text-sm text-slate-500">Choose multiple choice, true/false, or fill in the blank for each question.</p>
                        </div>
                        <button type="button" id="add-question" class="rounded-md border border-indigo-200 px-3 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-50">
                            + Add question
                        </button>
                    </div>

                    <div id="questions" class="space-y-5">
                        @foreach ($questionData as $questionIndex => $question)
                            <fieldset class="question-card space-y-4 rounded-lg border border-slate-200 p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <legend class="font-semibold text-slate-800">Question {{ $loop->iteration }}</legend>
                                    <button type="button" class="remove-question text-sm font-medium text-red-600 hover:text-red-800">Remove</button>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Question text</label>
                                    <textarea name="questions[{{ $questionIndex }}][question_text]" rows="2" required
                                        class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $question['question_text'] ?? '' }}</textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Question type</label>
                                    <select name="questions[{{ $questionIndex }}][type]" class="question-type mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="multiple_choice" @selected(($question['type'] ?? 'multiple_choice') === 'multiple_choice')>Multiple choice</option>
                                        <option value="true_false" @selected(($question['type'] ?? '') === 'true_false')>True / False</option>
                                        <option value="fill_blank" @selected(($question['type'] ?? '') === 'fill_blank')>Fill in the blank</option>
                                    </select>
                                </div>

                                <div class="max-w-40">
                                    <label class="block text-sm font-medium text-slate-700">Points</label>
                                    <input type="number" name="questions[{{ $questionIndex }}][points]" min="1" required value="{{ $question['points'] ?? 1 }}"
                                        class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div data-question-type="multiple_choice" class="question-type-fields grid gap-3 sm:grid-cols-2">
                                    @foreach (range(0, 3) as $choiceIndex)
                                        <div class="flex items-start gap-2">
                                            <input type="radio" name="questions[{{ $questionIndex }}][correct_choice]" value="{{ $choiceIndex }}" required
                                                {{ (int) ($question['correct_choice'] ?? 0) === $choiceIndex ? 'checked' : '' }}
                                                aria-label="Mark option {{ $choiceIndex + 1 }} as correct"
                                                class="mt-3 border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                            <div class="flex-1">
                                                <label class="block text-xs font-medium text-slate-500">Option {{ $choiceIndex + 1 }}</label>
                                                <input type="text" name="questions[{{ $questionIndex }}][choices][{{ $choiceIndex }}][text]" required maxlength="1000"
                                                    value="{{ $question['choices'][$choiceIndex]['text'] ?? '' }}"
                                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div data-question-type="true_false" class="question-type-fields">
                                    <label class="block text-sm font-medium text-slate-700">Correct answer</label>
                                    <select name="questions[{{ $questionIndex }}][true_false_answer]" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm">
                                        <option value="True" @selected(($question['true_false_answer'] ?? 'True') === 'True')>True</option>
                                        <option value="False" @selected(($question['true_false_answer'] ?? 'True') === 'False')>False</option>
                                    </select>
                                </div>

                                <div data-question-type="fill_blank" class="question-type-fields">
                                    <label class="block text-sm font-medium text-slate-700">Accepted answers</label>
                                    <textarea name="questions[{{ $questionIndex }}][accepted_answers]" rows="3" placeholder="One accepted answer per line"
                                        class="mt-1 block w-full rounded-md border-slate-300 shadow-sm">{{ $question['accepted_answers'] ?? '' }}</textarea>
                                    <p class="mt-1 text-xs text-slate-500">Learner answers are checked without case or extra-space differences. Add alternate correct spellings on separate lines.</p>
                                </div>
                            </fieldset>
                        @endforeach
                    </div>
                </section>

                <section class="flex flex-wrap items-center justify-between gap-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published', $quiz->is_published) ? 'checked' : '' }}
                            class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        Publish quiz (required before publishing the course)
                    </label>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('trainer.courses.show', $course->id) }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
                        <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Save quiz</button>
                    </div>
                </section>
            </form>

            <template id="question-template">
                <fieldset class="question-card space-y-4 rounded-lg border border-slate-200 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <legend class="font-semibold text-slate-800">Question</legend>
                        <button type="button" class="remove-question text-sm font-medium text-red-600 hover:text-red-800">Remove</button>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Question text</label>
                        <textarea name="questions[__QUESTION__][question_text]" rows="2" required class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Question type</label>
                        <select name="questions[__QUESTION__][type]" class="question-type mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="multiple_choice">Multiple choice</option>
                            <option value="true_false">True / False</option>
                            <option value="fill_blank">Fill in the blank</option>
                        </select>
                    </div>
                    <div class="max-w-40">
                        <label class="block text-sm font-medium text-slate-700">Points</label>
                        <input type="number" name="questions[__QUESTION__][points]" min="1" required value="1" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div data-question-type="multiple_choice" class="question-type-fields grid gap-3 sm:grid-cols-2">
                        @foreach (range(0, 3) as $choiceIndex)
                            <div class="flex items-start gap-2">
                                <input type="radio" name="questions[__QUESTION__][correct_choice]" value="{{ $choiceIndex }}" required aria-label="Mark option {{ $choiceIndex + 1 }} as correct" class="mt-3 border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <div class="flex-1">
                                    <label class="block text-xs font-medium text-slate-500">Option {{ $choiceIndex + 1 }}</label>
                                    <input type="text" name="questions[__QUESTION__][choices][{{ $choiceIndex }}][text]" required maxlength="1000" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div data-question-type="true_false" class="question-type-fields hidden">
                        <label class="block text-sm font-medium text-slate-700">Correct answer</label>
                        <select name="questions[__QUESTION__][true_false_answer]" disabled class="mt-1 block w-full rounded-md border-slate-300 shadow-sm">
                            <option value="True">True</option>
                            <option value="False">False</option>
                        </select>
                    </div>
                    <div data-question-type="fill_blank" class="question-type-fields hidden">
                        <label class="block text-sm font-medium text-slate-700">Accepted answers</label>
                        <textarea name="questions[__QUESTION__][accepted_answers]" rows="3" disabled placeholder="One accepted answer per line" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm"></textarea>
                        <p class="mt-1 text-xs text-slate-500">Add alternate correct answers on separate lines.</p>
                    </div>
                </fieldset>
            </template>
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
