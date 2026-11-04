<?php

use App\Models\Course;
use App\Models\User;
use App\Models\Workshop;

beforeEach(function () {
    $this->seed();
});

test('trainers can manage their workshops from the trainer portal', function () {
    $trainer = User::where('email', 'trainer@example.com')->firstOrFail();
    $course = Course::where('title', 'School Earthquake & Fire Readiness Protocol')->firstOrFail();
    expect($course->hasReadyModuleQuizzes())->toBeTrue();

    $this->actingAs($trainer)
        ->get(route('trainer.workshops.index'))
        ->assertOk()
        ->assertSee('Workshop Management')
        ->assertSee('Comprehensive Typhoon & Flood Safety Workshop')
        ->assertSeeInOrder(['Dashboard Overview', 'Workshop Management', 'Course Management']);

    $this->actingAs($trainer)
        ->get(route('trainer.workshops.create'))
        ->assertOk()
        ->assertSee('Create Workshop')
        ->assertSee($course->title);

    $start = now()->addDays(10)->startOfHour();
    $this->actingAs($trainer)
        ->post(route('trainer.workshops.store'), [
            'course_id' => $course->id,
            'title' => 'Earthquake Readiness Trainer Workshop',
            'description' => 'A trainer-led workshop for local educators.',
            'start_date' => $start->format('Y-m-d\TH:i'),
            'end_date' => $start->copy()->addDays(2)->format('Y-m-d\TH:i'),
            'registration_deadline' => $start->copy()->subDay()->format('Y-m-d\TH:i'),
            'status' => 'published',
        ])
        ->assertRedirect(route('trainer.workshops.index'))
        ->assertSessionHas('success', 'Workshop created successfully.');

    $workshop = Workshop::where('title', 'Earthquake Readiness Trainer Workshop')->firstOrFail();
    expect($workshop->trainer_id)->toBe($trainer->id);
    $this->assertDatabaseHas('workshops', [
        'id' => $workshop->id,
        'course_id' => $course->id,
        'status' => 'published',
    ]);

    $teacher = User::where('email', 'teacher@example.com')->firstOrFail();
    $this->actingAs($teacher)
        ->get(route('teacher.workshops.index'))
        ->assertOk()
        ->assertSee('Earthquake Readiness Trainer Workshop');

    $this->actingAs($trainer)
        ->get(route('trainer.workshops.edit', $workshop))
        ->assertOk()
        ->assertSee('Edit Workshop');
});

test('trainers cannot manage workshops owned by another trainer', function () {
    $owner = User::where('email', 'trainer@example.com')->firstOrFail();
    $otherTrainer = User::factory()->create(['role' => 'trainer']);
    $workshop = Workshop::where('trainer_id', $owner->id)->firstOrFail();

    $this->actingAs($otherTrainer)
        ->get(route('trainer.workshops.edit', $workshop))
        ->assertNotFound();

    $this->actingAs($otherTrainer)
        ->delete(route('trainer.workshops.destroy', $workshop))
        ->assertNotFound();
});

test('workshop schedule validation prevents an end date before start and deadline after start', function () {
    $trainer = User::where('email', 'trainer@example.com')->firstOrFail();
    $course = Course::where('title', 'School Earthquake & Fire Readiness Protocol')->firstOrFail();
    $start = now()->addDays(10)->startOfHour();

    $this->actingAs($trainer)
        ->post(route('trainer.workshops.store'), [
            'course_id' => $course->id,
            'title' => 'Invalid Dates Workshop',
            'start_date' => $start->format('Y-m-d\TH:i'),
            'end_date' => $start->copy()->subHour()->format('Y-m-d\TH:i'),
            'registration_deadline' => $start->copy()->addHour()->format('Y-m-d\TH:i'),
            'status' => 'draft',
        ])
        ->assertSessionHasErrors(['end_date', 'registration_deadline']);

    $this->assertDatabaseMissing('workshops', ['title' => 'Invalid Dates Workshop']);
});

test('trainers can create draft workshops from draft courses while course content is being prepared', function () {
    $trainer = User::where('email', 'trainer@example.com')->firstOrFail();
    $course = Course::where('title', 'School Earthquake & Fire Readiness Protocol')->firstOrFail();
    $course->update(['status' => 'draft']);

    $this->actingAs($trainer)
        ->get(route('trainer.workshops.create'))
        ->assertOk()
        ->assertSee($course->title)
        ->assertSee('Create workshop', false)
        ->assertDontSee('type="submit" disabled', false);

    $start = now()->addDays(10)->startOfHour();
    $this->actingAs($trainer)
        ->post(route('trainer.workshops.store'), [
            'course_id' => $course->id,
            'title' => 'Workshop Draft While Course Is Prepared',
            'start_date' => $start->format('Y-m-d\TH:i'),
            'end_date' => $start->copy()->addDay()->format('Y-m-d\TH:i'),
            'registration_deadline' => $start->copy()->subDay()->format('Y-m-d\TH:i'),
            'status' => 'draft',
        ])
        ->assertRedirect(route('trainer.workshops.index'));

    $this->assertDatabaseHas('workshops', [
        'title' => 'Workshop Draft While Course Is Prepared',
        'course_id' => $course->id,
        'status' => 'draft',
    ]);
});

test('trainers cannot publish workshops until their selected course and quizzes are ready', function () {
    $trainer = User::where('email', 'trainer@example.com')->firstOrFail();
    $course = Course::where('title', 'School Earthquake & Fire Readiness Protocol')->firstOrFail();
    $course->update(['status' => 'draft']);
    $start = now()->addDays(10)->startOfHour();

    $this->actingAs($trainer)
        ->post(route('trainer.workshops.store'), [
            'course_id' => $course->id,
            'title' => 'Prematurely Published Workshop',
            'start_date' => $start->format('Y-m-d\TH:i'),
            'end_date' => $start->copy()->addDay()->format('Y-m-d\TH:i'),
            'registration_deadline' => $start->copy()->subDay()->format('Y-m-d\TH:i'),
            'status' => 'published',
        ])
        ->assertSessionHasErrors('status');

    $this->assertDatabaseMissing('workshops', ['title' => 'Prematurely Published Workshop']);
});
