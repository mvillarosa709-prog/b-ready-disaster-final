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

test('trainers can publish a ready draft workshop in one click and manage its classroom package', function () {
    \Illuminate\Support\Facades\Storage::fake('public');

    $trainer = User::where('email', 'trainer@example.com')->firstOrFail();
    $course = Course::where('title', 'School Earthquake & Fire Readiness Protocol')->firstOrFail();
    $start = now()->addDays(10)->startOfHour();

    $workshop = Workshop::create([
        'course_id' => $course->id,
        'trainer_id' => $trainer->id,
        'title' => 'One Click Publish Cohort',
        'description' => 'Ready cohort',
        'start_date' => $start,
        'end_date' => $start->copy()->addDays(2),
        'registration_deadline' => $start->copy()->subDay(),
        'status' => 'draft',
    ]);

    $this->actingAs($trainer)
        ->get(route('trainer.workshops.show', $workshop))
        ->assertOk()
        ->assertSee('Publish Workshop')
        ->assertSee('Quiz Ready')
        ->assertDontSee('Quiz Pending');

    $this->actingAs($trainer)
        ->post(route('trainer.workshops.publish', $workshop))
        ->assertRedirect(route('trainer.workshops.show', $workshop));

    expect($workshop->fresh()->status)->toBe('published');
    expect($workshop->fresh()->classroomPackage)->not->toBeNull();

    $this->actingAs($trainer)
        ->get(route('trainer.workshops.show', $workshop))
        ->assertOk()
        ->assertDontSee('Switch to Draft');

    $this->actingAs($trainer)
        ->post(route('trainer.workshops.package.save', $workshop), [
            'title' => 'Custom Classroom Toolkit',
            'description' => 'Ready for teachers',
            'is_published' => '1',
        ])
        ->assertRedirect(route('trainer.workshops.show', $workshop));

    $this->actingAs($trainer)
        ->post(route('trainer.workshops.package.materials.store', $workshop), [
            'title' => 'Classroom Drill Guide',
            'material_type' => 'teacher_guide',
            'description' => 'Step-by-step instructions',
            'file' => \Illuminate\Http\UploadedFile::fake()->create('drill-guide.pdf', 100, 'application/pdf'),
        ])
        ->assertRedirect(route('trainer.workshops.show', $workshop));

    $this->assertDatabaseHas('classroom_packages', [
        'workshop_id' => $workshop->id,
        'title' => 'Custom Classroom Toolkit',
        'is_published' => true,
    ]);
    $this->assertDatabaseHas('classroom_materials', [
        'title' => 'Classroom Drill Guide',
        'material_type' => 'teacher_guide',
    ]);
});
