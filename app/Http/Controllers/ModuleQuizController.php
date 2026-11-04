<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Module;
use App\Services\ModuleQuizSpreadsheetService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ModuleQuizController extends Controller
{
    public function edit(int $courseId, int $moduleId)
    {
        $course = Course::where('trainer_id', auth()->id())->findOrFail($courseId);
        $module = Module::where('course_id', $course->id)->findOrFail($moduleId);
        $quiz = $module->quiz()->with('questions.choices')->firstOrFail();

        $questionData = old('questions');
        if ($questionData === null) {
            $questionData = $quiz->questions->map(function ($question) {
                $choices = array_fill(0, 4, ['text' => '']);
                $correctChoice = 0;
                $acceptedAnswers = $question->choices
                    ->where('is_correct', true)
                    ->pluck('choice_text')
                    ->all();

                foreach ($question->choices->take(4)->values() as $index => $choice) {
                    $choices[$index] = ['text' => $choice->choice_text];
                    if ($choice->is_correct) {
                        $correctChoice = $index;
                    }
                }

                return [
                    'question_text' => $question->question_text,
                    'points' => $question->points,
                    'type' => $question->question_type,
                    'choices' => $choices,
                    'correct_choice' => $correctChoice,
                    'true_false_answer' => $question->choices->firstWhere('is_correct', true)?->choice_text ?? 'True',
                    'accepted_answers' => implode("\n", $acceptedAnswers),
                ];
            })->all();

            if ($questionData === []) {
                $questionData = [[
                    'question_text' => '',
                    'points' => 1,
                    'type' => 'multiple_choice',
                    'choices' => array_fill(0, 4, ['text' => '']),
                    'correct_choice' => 0,
                    'true_false_answer' => 'True',
                    'accepted_answers' => '',
                ]];
            }
        }

        return view('trainer.modules.quiz', compact('course', 'module', 'quiz', 'questionData'));
    }

    public function update(Request $request, int $courseId, int $moduleId)
    {
        $course = Course::where('trainer_id', auth()->id())->findOrFail($courseId);
        $module = Module::where('course_id', $course->id)->findOrFail($moduleId);
        $quiz = $module->quiz()->firstOrFail();

        if ($quiz->attempts()->exists()) {
            return back()->withErrors([
                'questions' => 'This quiz cannot be changed after a learner has attempted it.',
            ]);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'passing_score' => ['required', 'integer', 'min:1', 'max:100'],
            'time_limit' => ['required', 'integer', 'min:1'],
            'max_attempts' => ['required', 'integer', 'min:1'],
            'is_published' => ['nullable', 'boolean'],
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.question_text' => ['required', 'string'],
            'questions.*.points' => ['required', 'integer', 'min:1'],
            'questions.*.type' => ['required', 'in:multiple_choice,true_false,fill_blank'],
            'questions.*.choices' => ['nullable', 'array'],
            'questions.*.choices.*.text' => ['nullable', 'string', 'max:1000'],
            'questions.*.correct_choice' => ['nullable', 'integer', 'between:0,3'],
            'questions.*.true_false_answer' => ['nullable', 'in:True,False'],
            'questions.*.accepted_answers' => ['nullable', 'string', 'max:5000'],
        ]);

        $validated['questions'] = $this->normalizeQuestions($validated['questions']);

        DB::transaction(function () use ($validated, $request, $quiz, $course) {
            $isPublished = $request->boolean('is_published');
            $quiz->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'passing_score' => $validated['passing_score'],
                'time_limit' => $validated['time_limit'],
                'max_attempts' => $validated['max_attempts'],
                'is_published' => $isPublished,
            ]);

            $quiz->questions()->delete();

            foreach ($validated['questions'] as $questionIndex => $questionData) {
                $question = $quiz->questions()->create([
                    'question_text' => $questionData['question_text'],
                    'question_type' => $questionData['type'],
                    'points' => $questionData['points'],
                    'sequence' => $questionIndex + 1,
                ]);

                foreach ($questionData['choices'] as $choiceIndex => $choiceData) {
                    $question->choices()->create([
                        'choice_text' => $choiceData['text'],
                        'is_correct' => $choiceData['is_correct'],
                        'sequence' => $choiceIndex + 1,
                    ]);
                }
            }

            if (! $isPublished && $course->status === 'published') {
                $course->update(['status' => 'draft']);
            }
        });

        return redirect()
            ->route('trainer.courses.show', $course->id)
            ->with('success', $quiz->is_published
                ? 'Module quiz saved and published.'
                : 'Module quiz saved as a draft.');
    }

    public function downloadTemplate(int $courseId, int $moduleId)
    {
        Course::where('trainer_id', auth()->id())->findOrFail($courseId)
            ->modules()->findOrFail($moduleId);

        $workbook = app(ModuleQuizSpreadsheetService::class)->createTemplate();

        return response($workbook, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="B-READY-module-quiz-template.xlsx"',
        ]);
    }

    public function import(Request $request, int $courseId, int $moduleId)
    {
        $course = Course::where('trainer_id', auth()->id())->findOrFail($courseId);
        $module = $course->modules()->findOrFail($moduleId);
        $quiz = $module->quiz()->firstOrFail();

        if ($quiz->attempts()->exists()) {
            return back()->withErrors([
                'quiz_file' => 'This quiz cannot be replaced after a learner has attempted it.',
            ]);
        }

        $validated = $request->validate([
            'quiz_file' => ['required', 'file', 'max:5120'],
        ]);

        /** @var UploadedFile $file */
        $file = $validated['quiz_file'];
        $extension = strtolower($file->getClientOriginalExtension());
        if (! in_array($extension, ['csv', 'xlsx'], true)) {
            throw ValidationException::withMessages([
                'quiz_file' => 'Upload a CSV or Excel .xlsx file. Use the downloadable template for the required columns.',
            ]);
        }

        $questions = $extension === 'xlsx'
            ? $this->parseSpreadsheet($file)
            : $this->parseCsv($file);

        DB::transaction(function () use ($questions, $quiz, $course) {
            $quiz->questions()->delete();

            foreach ($questions as $questionIndex => $questionData) {
                $question = $quiz->questions()->create([
                    'question_text' => $questionData['question'],
                    'question_type' => $questionData['type'],
                    'points' => $questionData['points'],
                    'sequence' => $questionIndex + 1,
                ]);

                foreach ($questionData['options'] as $choiceIndex => $choiceText) {
                    $question->choices()->create([
                        'choice_text' => $choiceText,
                        'is_correct' => in_array($choiceText, $questionData['accepted_answers'], true),
                        'sequence' => $choiceIndex + 1,
                    ]);
                }
            }

            $quiz->update(['is_published' => false]);

            if ($course->status === 'published') {
                $course->update(['status' => 'draft']);
            }
        });

        return redirect()
            ->route('trainer.modules.quiz.edit', [$course->id, $module->id])
            ->with('success', count($questions).' quiz question(s) imported. Review the quiz and publish it when ready.');
    }

    /**
     * @return list<array{question: string, type: string, options: list<string>, accepted_answers: list<string>, points: int}>
     */
    private function parseCsv(UploadedFile $file): array
    {
        $stream = fopen($file->getRealPath(), 'r');
        if ($stream === false) {
            throw ValidationException::withMessages([
                'quiz_file' => 'The uploaded CSV file could not be read.',
            ]);
        }

        try {
            $headers = fgetcsv($stream, null, ',', '"', '');
            if ($headers === false) {
                throw ValidationException::withMessages([
                    'quiz_file' => 'The uploaded CSV file is empty.',
                ]);
            }

            $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $headers[0]);
            $headers = array_map(fn ($header) => strtolower(trim((string) $header)), $headers);
            $expectedHeaders = ['type', 'question', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer', 'accepted_answers', 'points'];

            if ($headers !== $expectedHeaders) {
                throw ValidationException::withMessages([
                    'quiz_file' => 'The CSV headers do not match the template. Download a fresh template and try again.',
                ]);
            }

            $questions = [];
            $rowNumber = 1;
            while (($row = fgetcsv($stream, null, ',', '"', '')) !== false) {
                $rowNumber++;
                $row = array_map(fn ($value) => trim((string) $value), $row);

                if ($row === [] || count(array_filter($row, fn ($value) => $value !== '')) === 0) {
                    continue;
                }

                if (count($row) !== count($expectedHeaders)) {
                    $this->invalidQuestionRow($rowNumber, 'each question must have exactly 9 columns.');
                }

                [$type, $questionText, $optionA, $optionB, $optionC, $optionD, $correctAnswer, $acceptedAnswers, $points] = $row;
                $options = [$optionA, $optionB, $optionC, $optionD];

                if ($questionText === '' || strlen($questionText) > 5000) {
                    $this->invalidQuestionRow($rowNumber, 'question text is required and must be 5,000 characters or fewer.');
                }

                if (! in_array($type, ['multiple_choice', 'true_false', 'fill_blank'], true)) {
                    $this->invalidQuestionRow($rowNumber, 'type must be multiple_choice, true_false, or fill_blank.');
                }

                if ($type === 'multiple_choice') {
                    foreach ($options as $option) {
                        if ($option === '' || strlen($option) > 1000) {
                            $this->invalidQuestionRow($rowNumber, 'multiple-choice questions require all four options (up to 1,000 characters each).');
                        }
                    }

                    $correctAnswer = strtoupper($correctAnswer);
                    if (! in_array($correctAnswer, ['A', 'B', 'C', 'D'], true)) {
                        $this->invalidQuestionRow($rowNumber, 'for multiple_choice, correct_answer must be A, B, C, or D.');
                    }
                    $accepted = [$options[ord($correctAnswer) - ord('A')]];
                } elseif ($type === 'true_false') {
                    $correctAnswer = ucfirst(strtolower($correctAnswer));
                    if (! in_array($correctAnswer, ['True', 'False'], true)) {
                        $this->invalidQuestionRow($rowNumber, 'for true_false, correct_answer must be True or False.');
                    }
                    $options = ['True', 'False'];
                    $accepted = [$correctAnswer];
                } else {
                    $accepted = array_values(array_unique(array_filter(
                        array_map('trim', explode('|', $acceptedAnswers)),
                        fn ($answer) => $answer !== ''
                    )));
                    if ($accepted === [] || count($accepted) > 20) {
                        $this->invalidQuestionRow($rowNumber, 'fill_blank questions require 1 to 20 accepted answers separated by | in accepted_answers.');
                    }
                    foreach ($accepted as $answer) {
                        if (strlen($answer) > 1000) {
                            $this->invalidQuestionRow($rowNumber, 'each accepted answer must be 1,000 characters or fewer.');
                        }
                    }
                    $options = $accepted;
                }

                if (! ctype_digit($points) || (int) $points < 1 || (int) $points > 1000) {
                    $this->invalidQuestionRow($rowNumber, 'points must be a whole number from 1 to 1,000.');
                }

                $questions[] = [
                    'question' => $questionText,
                    'type' => $type,
                    'options' => $options,
                    'accepted_answers' => $accepted,
                    'points' => (int) $points,
                ];

                if (count($questions) > 500) {
                    $this->invalidQuestionRow($rowNumber, 'a quiz can contain no more than 500 questions.');
                }
            }
        } finally {
            fclose($stream);
        }

        if ($questions === []) {
            throw ValidationException::withMessages([
                'quiz_file' => 'The CSV file must contain at least one question row.',
            ]);
        }

        return $questions;
    }

    private function parseSpreadsheet(UploadedFile $file): array
    {
        $rows = app(ModuleQuizSpreadsheetService::class)->readQuestions($file);
        if ($rows === []) {
            throw ValidationException::withMessages([
                'quiz_file' => 'The Questions worksheet is empty.',
            ]);
        }

        $temporaryCsv = tempnam(sys_get_temp_dir(), 'bready-quiz-csv-');
        if ($temporaryCsv === false) {
            throw new \RuntimeException('Unable to prepare the Excel quiz for import.');
        }

        try {
            $stream = fopen($temporaryCsv, 'w');
            if ($stream === false) {
                throw new \RuntimeException('Unable to prepare the Excel quiz for import.');
            }

            foreach ($rows as $row) {
                fputcsv($stream, $row);
            }
            fclose($stream);

            return $this->parseCsv(new UploadedFile(
                $temporaryCsv,
                $file->getClientOriginalName().'.csv',
                'text/csv',
                null,
                true
            ));
        } finally {
            if (is_file($temporaryCsv)) {
                unlink($temporaryCsv);
            }
        }
    }

    private function invalidQuestionRow(int $rowNumber, string $reason): never
    {
        throw ValidationException::withMessages([
            'quiz_file' => "Question row {$rowNumber}: {$reason}",
        ]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $questions
     * @return array<int, array{question_text: string, type: string, points: int, choices: array<int, array{text: string, is_correct: bool}>}>
     */
    private function normalizeQuestions(array $questions): array
    {
        foreach ($questions as $index => &$question) {
            $rawChoices = $question['choices'] ?? [];
            $question['choices'] = [];

            if ($question['type'] === 'multiple_choice') {
                if (! is_array($rawChoices) || count($rawChoices) !== 4) {
                    throw ValidationException::withMessages([
                        "questions.{$index}.choices" => 'Multiple-choice questions require exactly four options.',
                    ]);
                }

                $correctChoice = $question['correct_choice'] ?? null;
                if ($correctChoice === null) {
                    throw ValidationException::withMessages([
                        "questions.{$index}.correct_choice" => 'Select the correct option.',
                    ]);
                }

                foreach (array_values($rawChoices) as $choiceIndex => $choice) {
                    $text = trim((string) ($choice['text'] ?? ''));
                    if ($text === '') {
                        throw ValidationException::withMessages([
                            "questions.{$index}.choices.{$choiceIndex}.text" => 'All four choices are required.',
                        ]);
                    }
                    $question['choices'][] = [
                        'text' => $text,
                        'is_correct' => $choiceIndex === (int) $correctChoice,
                    ];
                }
            } elseif ($question['type'] === 'true_false') {
                $answer = $question['true_false_answer'] ?? null;
                if (! in_array($answer, ['True', 'False'], true)) {
                    throw ValidationException::withMessages([
                        "questions.{$index}.true_false_answer" => 'Select True or False as the correct answer.',
                    ]);
                }
                $question['choices'] = [
                    ['text' => 'True', 'is_correct' => $answer === 'True'],
                    ['text' => 'False', 'is_correct' => $answer === 'False'],
                ];
            } else {
                $accepted = array_values(array_unique(array_filter(
                    array_map('trim', preg_split('/[\r\n|]+/', (string) ($question['accepted_answers'] ?? '')) ?: []),
                    fn ($answer) => $answer !== ''
                )));
                if ($accepted === [] || count($accepted) > 20) {
                    throw ValidationException::withMessages([
                        "questions.{$index}.accepted_answers" => 'Add 1 to 20 accepted answers, one per line.',
                    ]);
                }
                foreach ($accepted as $answer) {
                    if (strlen($answer) > 1000) {
                        throw ValidationException::withMessages([
                            "questions.{$index}.accepted_answers" => 'Each accepted answer must be 1,000 characters or fewer.',
                        ]);
                    }
                    $question['choices'][] = ['text' => $answer, 'is_correct' => true];
                }
            }
        }
        unset($question);

        return $questions;
    }
}
