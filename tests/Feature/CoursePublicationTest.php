<?php

use App\Models\Course;
use App\Models\User;
use App\Models\Workshop;

beforeEach(function () {
    $this->seed();
});

test('new courses default to draft', function () {
    $trainer = User::where('email', 'trainer@example.com')->firstOrFail();

    $course = Course::create([
        'trainer_id' => $trainer->id,
        'title' => 'Draft course',
        'description' => 'Course description',
        'learning_objectives' => 'Course objectives',
        'target_participants' => 'Teachers',
        'estimated_duration' => 1,
    ]);

    expect($course->status)->toBe('draft');

    $this->actingAs($trainer)
        ->get(route('trainer.courses.create'))
        ->assertOk()
        ->assertDontSee('name="status"', false)
        ->assertDontSee('Publication Status');

    $response = $this->actingAs($trainer)
        ->post(route('trainer.courses.store'), [
            'title' => 'Course without a status',
            'description' => 'Course description',
            'learning_objectives' => 'Course objectives',
            'target_participants' => 'Teachers',
            'estimated_duration' => 1,
            'status' => 'published',
        ]);

    $createdCourse = Course::where('title', 'Course without a status')->firstOrFail();

    $response->assertRedirect(route('trainer.modules.create', $createdCourse->id));

    $this->assertDatabaseHas('courses', [
        'id' => $createdCourse->id,
        'title' => 'Course without a status',
        'status' => 'draft',
    ]);

    $this->actingAs($trainer)
        ->get(route('trainer.courses.show', $createdCourse->id))
        ->assertOk()
        ->assertDontSee('Publish Course');
});

test('trainers can publish only completed courses and see unpublish only after publication', function () {
    $trainer = User::where('email', 'trainer@example.com')->firstOrFail();
    $course = Course::where('title', 'School Earthquake & Fire Readiness Protocol')->firstOrFail();
    $course->update(['status' => 'draft']);

    $this->actingAs($trainer)
        ->get(route('trainer.courses.show', $course->id))
        ->assertOk()
        ->assertSee('Publish Course')
        ->assertDontSee('Unpublish Course');

    $this->actingAs($trainer)
        ->post(route('trainer.courses.publish', $course->id))
        ->assertRedirect(route('trainer.courses.show', $course->id));

    $this->assertDatabaseHas('courses', ['id' => $course->id, 'status' => 'published']);

    $this->actingAs($trainer)
        ->get(route('trainer.courses.show', $course->id))
        ->assertOk()
        ->assertSee('Unpublish Course')
        ->assertDontSee('Publish Course');

    $this->actingAs($trainer)
        ->post(route('trainer.courses.unpublish', $course->id))
        ->assertRedirect(route('trainer.courses.show', $course->id));

    $this->assertDatabaseHas('courses', ['id' => $course->id, 'status' => 'unpublished']);
});

test('draft courses are hidden from teachers but visible to trainers', function () {
    $course = Course::where('title', 'School Earthquake & Fire Readiness Protocol')->firstOrFail();
    $course->update(['status' => 'draft']);

    $teacher = User::where('email', 'teacher@example.com')->firstOrFail();
    $trainer = User::where('email', 'trainer@example.com')->firstOrFail();
    $workshop = Workshop::where('course_id', $course->id)->firstOrFail();

    $this->actingAs($teacher)
        ->get(route('teacher.workshops.index'))
        ->assertOk()
        ->assertDontSee($workshop->title);

    $this->actingAs($teacher)
        ->get(route('teacher.workshops.show', $workshop))
        ->assertForbidden();

    $this->actingAs($trainer)
        ->get(route('trainer.courses.index'))
        ->assertOk()
        ->assertSee($course->title)
        ->assertSee('Draft');
});

test('courses with published fill-in-the-blank module quizzes can be published', function () {
    $trainer = User::where('email', 'trainer@example.com')->firstOrFail();

    $course = Course::create([
        'trainer_id' => $trainer->id,
        'title' => 'Fill Blank Course',
        'description' => 'Course description',
        'learning_objectives' => 'Course objectives',
        'target_participants' => 'Teachers',
        'estimated_duration' => 30,
        'status' => 'draft',
    ]);

    $module = $course->modules()->create([
        'title' => 'Module 1',
        'description' => 'Module description',
        'learning_objectives' => 'Module objectives',
        'estimated_duration' => 15,
        'sequence' => 1,
        'is_required' => true,
    ]);

    $quiz = $module->quiz()->create([
        'trainer_id' => $trainer->id,
        'title' => 'Module 1 Quiz',
        'description' => 'Answer carefully',
        'passing_score' => 75,
        'time_limit' => 30,
        'max_attempts' => 3,
        'is_published' => false,
    ]);

    $question = $quiz->questions()->create([
        'question_text' => 'What stands for DRRM?',
        'question_type' => 'fill_blank',
        'points' => 1,
        'sequence' => 1,
    ]);
    $question->choices()->create([
        'choice_text' => 'Disaster Risk Reduction and Management',
        'is_correct' => true,
        'sequence' => 1,
    ]);

    expect($course->fresh()->hasReadyModuleQuizzes())->toBeFalse();

    $quiz->update(['is_published' => true]);

    expect($course->fresh()->hasReadyModuleQuizzes())->toBeTrue();

    $this->actingAs($trainer)
        ->post(route('trainer.courses.publish', $course->id))
        ->assertRedirect(route('trainer.courses.show', $course->id));

    $this->assertDatabaseHas('courses', ['id' => $course->id, 'status' => 'published']);
});
