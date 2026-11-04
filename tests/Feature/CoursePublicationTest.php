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

    $this->actingAs($trainer)
        ->post(route('trainer.courses.store'), [
            'title' => 'Course without a status',
            'description' => 'Course description',
            'learning_objectives' => 'Course objectives',
            'target_participants' => 'Teachers',
            'estimated_duration' => 1,
            'status' => 'published',
        ])
        ->assertRedirect(route('trainer.courses.index'));

    $this->assertDatabaseHas('courses', [
        'title' => 'Course without a status',
        'status' => 'draft',
    ]);
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
