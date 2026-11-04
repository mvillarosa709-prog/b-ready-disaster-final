<?php

use App\Models\AssessmentAttempt;
use App\Models\Course;
use App\Models\Module;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopTeacher;
use Illuminate\Http\UploadedFile;

beforeEach(function () {
    $this->seed();
});

test('trainers can author a required quiz for each module before publishing a course', function () {
    $trainer = User::where('email', 'trainer@example.com')->firstOrFail();
    $course = Course::where('title', 'School Earthquake & Fire Readiness Protocol')->firstOrFail();

    $this->actingAs($trainer)
        ->post(route('trainer.modules.store', $course->id), [
            'title' => 'Emergency Communication',
            'description' => 'Communication protocols during an emergency.',
            'learning_objectives' => 'Share verified updates with the response team.',
            'estimated_duration' => 25,
            'sequence' => 4,
            'is_required' => '1',
        ])
        ->assertRedirect(route('trainer.courses.show', $course->id));

    $module = Module::where('course_id', $course->id)
        ->where('title', 'Emergency Communication')
        ->firstOrFail();
    $quiz = $module->quiz()->firstOrFail();

    expect($quiz->is_published)->toBeFalse();
    expect($module->estimated_duration)->toBe(25);
    $this->assertDatabaseHas('courses', ['id' => $course->id, 'status' => 'draft']);

    $this->actingAs($trainer)
        ->get(route('trainer.modules.quiz.edit', [$course->id, $module->id]))
        ->assertOk()
        ->assertSee('Quiz questions');

    $this->actingAs($trainer)
        ->put(route('trainer.modules.quiz.update', [$course->id, $module->id]), [
            'title' => 'Emergency Communication Check',
            'description' => 'Check how to communicate verified emergency updates.',
            'passing_score' => 75,
            'time_limit' => 10,
            'max_attempts' => 3,
            'is_published' => '1',
            'questions' => [
                [
                    'question_text' => 'What should a teacher share during an emergency?',
                    'type' => 'multiple_choice',
                    'points' => 2,
                    'correct_choice' => 1,
                    'choices' => [
                        ['text' => 'Unverified rumors'],
                        ['text' => 'Verified information through the response channel'],
                        ['text' => 'Private student information in a public group'],
                        ['text' => 'No information under any circumstances'],
                    ],
                ],
            ],
        ])
        ->assertRedirect(route('trainer.courses.show', $course->id));

    $quiz->refresh();
    expect($quiz->is_published)->toBeTrue();
    expect($quiz->questions()->count())->toBe(1);
    expect($quiz->questions()->first()->choices()->where('is_correct', true)->count())->toBe(1);

    $this->actingAs($trainer)
        ->post(route('trainer.courses.publish', $course->id))
        ->assertRedirect(route('trainer.courses.show', $course->id));

    $this->assertDatabaseHas('courses', ['id' => $course->id, 'status' => 'published']);
});

test('a shared module quiz tracks attempts separately for each workshop', function () {
    $teacher = User::factory()->create(['role' => 'teacher']);
    $workshop = Workshop::where('title', 'like', '%Earthquake%')->firstOrFail();
    $course = $workshop->course;
    $module = $course->modules()->orderBy('sequence')->firstOrFail();
    $quiz = $module->quiz()->with('questions.choices')->firstOrFail();

    $secondWorkshop = Workshop::create([
        'course_id' => $course->id,
        'trainer_id' => $course->trainer_id,
        'title' => 'Second Earthquake Cohort',
        'description' => 'A second cohort using the same module content.',
        'start_date' => now(),
        'end_date' => now()->addDays(3),
        'registration_deadline' => now()->addDay(),
        'status' => 'ongoing',
    ]);

    foreach ([$workshop, $secondWorkshop] as $cohort) {
        WorkshopTeacher::create([
            'workshop_id' => $cohort->id,
            'teacher_id' => $teacher->id,
            'status' => 'registered',
            'joined_at' => now(),
        ]);
    }

    $answers = [];
    foreach ($quiz->questions as $question) {
        $correctChoice = $question->choices->firstWhere('is_correct', true);
        $answers[$question->id] = $question->question_type === 'fill_blank'
            ? $correctChoice->choice_text
            : (string) $correctChoice->id;
    }

    foreach ([$workshop, $secondWorkshop] as $cohort) {
        $response = $this->actingAs($teacher)
            ->post(route('teacher.learning.module.assessment.submit', [$cohort, $module]), [
                'answers' => $answers,
            ]);
        $response->assertRedirect();
    }

    expect(AssessmentAttempt::where('assessment_id', $quiz->id)
        ->where('teacher_id', $teacher->id)
        ->where('attempt_number', 1)
        ->count())->toBe(2);
});

test('trainers can import a complete question set from CSV', function () {
    $trainer = User::where('email', 'trainer@example.com')->firstOrFail();
    $module = Module::where('title', 'Fundamentals of Seismic Hazards & School Risk Assessment')->firstOrFail();
    $quiz = $module->quiz()->firstOrFail();

    $csv = implode("\r\n", [
        'type,question,option_a,option_b,option_c,option_d,correct_answer,accepted_answers,points',
        'multiple_choice,"During an earthquake, which action is safest?","Run outside","Duck, cover, and hold","Use the elevator","Stand by windows",B,,2',
        'multiple_choice,"Which item belongs in a go-bag?","Emergency water","Glass decorations","Loose batteries","Unlabeled chemicals",A,,3',
    ]);

    $this->actingAs($trainer)
        ->post(route('trainer.modules.quiz.import', [$module->course_id, $module->id]), [
            'quiz_file' => UploadedFile::fake()->createWithContent('module-quiz.csv', $csv),
        ])
        ->assertRedirect(route('trainer.modules.quiz.edit', [$module->course_id, $module->id]))
        ->assertSessionHas('success', '2 quiz question(s) imported. Review the quiz and publish it when ready.');

    $quiz->refresh();
    expect($quiz->is_published)->toBeFalse();
    expect($quiz->questions()->count())->toBe(2);
    expect($quiz->questions()->first()->question_text)->toBe('During an earthquake, which action is safest?');
    expect($quiz->questions()->first()->choices()->where('is_correct', true)->value('choice_text'))
        ->toBe('Duck, cover, and hold');
    expect($quiz->questions()->sum('points'))->toBe(5);
});

test('formatted Excel template imports all supported question types', function () {
    $trainer = User::where('email', 'trainer@example.com')->firstOrFail();
    $module = Module::where('title', 'Fundamentals of Seismic Hazards & School Risk Assessment')->firstOrFail();
    $quiz = $module->quiz()->firstOrFail();

    $templateResponse = $this->actingAs($trainer)
        ->get(route('trainer.modules.quiz.template', [$module->course_id, $module->id]))
        ->assertOk()
        ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
        ->assertHeader('content-disposition', 'attachment; filename="B-READY-module-quiz-template.xlsx"');

    $this->actingAs($trainer)
        ->post(route('trainer.modules.quiz.import', [$module->course_id, $module->id]), [
            'quiz_file' => UploadedFile::fake()->createWithContent('module-quiz.xlsx', $templateResponse->getContent()),
        ])
        ->assertRedirect(route('trainer.modules.quiz.edit', [$module->course_id, $module->id]))
        ->assertSessionHas('success', '3 quiz question(s) imported. Review the quiz and publish it when ready.');

    expect($quiz->fresh()->questions()->count())->toBe(3);
    expect($quiz->fresh()->questions()->pluck('question_type')->all())
        ->toBe(['multiple_choice', 'true_false', 'fill_blank']);
    expect($quiz->fresh()->questions()->orderBy('sequence')->skip(2)->first()->choices()->where('is_correct', true)->pluck('choice_text')->all())
        ->toBe(['area', 'point', 'zone']);
});

test('invalid quiz CSV imports do not replace existing questions', function () {
    $trainer = User::where('email', 'trainer@example.com')->firstOrFail();
    $module = Module::where('title', 'Fundamentals of Seismic Hazards & School Risk Assessment')->firstOrFail();
    $quiz = $module->quiz()->with('questions')->firstOrFail();
    $originalQuestionIds = $quiz->questions->pluck('id')->all();
    $csv = implode("\r\n", [
        'type,question,option_a,option_b,option_c,option_d,correct_answer,accepted_answers,points',
        'multiple_choice,"Incomplete row","First option","Second option",,,Z,,1',
    ]);

    $this->actingAs($trainer)
        ->post(route('trainer.modules.quiz.import', [$module->course_id, $module->id]), [
            'quiz_file' => UploadedFile::fake()->createWithContent('broken.csv', $csv),
        ])
        ->assertSessionHasErrors('quiz_file');

    expect($quiz->fresh()->questions()->pluck('id')->all())->toBe($originalQuestionIds);
});
