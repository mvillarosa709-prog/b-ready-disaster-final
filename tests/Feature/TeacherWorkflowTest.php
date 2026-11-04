<?php

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Certification;
use App\Models\Choice;
use App\Models\ClassroomImplementation;
use App\Models\Module;
use App\Models\ModuleProgress;
use App\Models\Question;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopTeacher;
use App\Notifications\CertificatePendingApproval;
use App\Services\AssessmentEvaluationService;
use App\Services\ClassroomImplementationService;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->seed();
});

test('teacher can access dashboard', function () {
    $teacher = User::where('email', 'teacher@example.com')->first();

    $response = $this->actingAs($teacher)->get(route('teacher.dashboard'));

    $response->assertOk();
    $response->assertSee('Welcome back');
    $response->assertSee('NCR Regional Teachers Earthquake Preparedness');
    $response->assertSee('Find Workshops');
    $response->assertSee('b-ready-logo.png');
    $response->assertDontSee('Signed in as');
});

test('teacher can browse available workshops', function () {
    $teacher = User::where('email', 'teacher@example.com')->first();
    $closedWorkshop = Workshop::where('title', 'like', 'Fire Prevention & Lab Safety Masterclass%')->firstOrFail();
    $closedWorkshop->update(['registration_deadline' => now()->subDay()]);

    $response = $this->actingAs($teacher)->get(route('teacher.workshops.index'));

    $response->assertOk();
    $response->assertSee('Available Workshops');
    $response->assertSee('Fire Prevention & Lab Safety Masterclass');
    $response->assertSee('Registration Closed');
});

test('teacher can view my workshops', function () {
    $teacher = User::where('email', 'certified.teacher@example.com')->first();

    $response = $this->actingAs($teacher)->get(route('teacher.workshops.my'));

    $response->assertOk();
    $response->assertSee('My Workshops');
    $response->assertSee('Comprehensive Typhoon & Flood Safety');
});

test('teacher can enroll in an open workshop and starts at module 1', function () {
    $teacher = User::factory()->create([
        'role' => 'teacher',
        'email_verified_at' => now(),
    ]);

    $workshop = Workshop::where('status', 'published')->first();
    $firstModule = $workshop->ordered_modules->sortBy('sequence')->first();

    $response = $this->actingAs($teacher)->post(route('teacher.workshops.join', ['workshop' => $workshop->id]));

    if ($firstModule) {
        $response->assertRedirect(route('teacher.learning.module', ['workshop' => $workshop->id, 'module' => $firstModule->id]));
    } else {
        $response->assertRedirect(route('teacher.workshops.show', ['workshop' => $workshop->id]));
    }

    $this->assertDatabaseHas('workshop_teachers', [
        'workshop_id' => $workshop->id,
        'teacher_id' => $teacher->id,
    ]);
});

test('teacher cannot access module without enrolling first', function () {
    $teacher = User::factory()->create(['role' => 'teacher', 'email_verified_at' => now()]);
    $workshop = Workshop::where('status', 'published')->first();
    $firstModule = $workshop->ordered_modules->sortBy('sequence')->first();

    $response = $this->actingAs($teacher)->get(route('teacher.learning.module', ['workshop' => $workshop->id, 'module' => $firstModule->id]));

    $response->assertRedirect(route('teacher.workshops.show', ['workshop' => $workshop->id]));
    $response->assertSessionHas('warning');
});

test('sequential learning policy blocks skipping ahead to locked module', function () {
    $newTeacher = User::factory()->create([
        'role' => 'teacher',
        'email_verified_at' => now(),
    ]);

    $workshop1 = Workshop::where('title', 'like', '%Earthquake%')->first();
    $mod2 = $workshop1->course->modules->where('sequence', 2)->first();

    WorkshopTeacher::create([
        'workshop_id' => $workshop1->id,
        'teacher_id' => $newTeacher->id,
        'status' => 'registered',
        'joined_at' => now(),
    ]);

    // Teacher has NOT completed Module 1 yet, tries to jump to Module 2
    $response = $this->actingAs($newTeacher)->get(route('teacher.learning.module', ['workshop' => $workshop1->id, 'module' => $mod2->id]));

    // Should be forbidden by policy
    $response->assertForbidden();
});

test('teacher completing module 1 unlocks module 2', function () {
    $teacher = User::factory()->create(['role' => 'teacher', 'email_verified_at' => now()]);
    $workshop1 = Workshop::where('title', 'like', '%Earthquake%')->first();
    $mod1 = $workshop1->course->modules->where('sequence', 1)->first();
    $mod2 = $workshop1->course->modules->where('sequence', 2)->first();

    WorkshopTeacher::create([
        'workshop_id' => $workshop1->id,
        'teacher_id' => $teacher->id,
        'user_id' => $teacher->id,
        'status' => 'registered',
        'joined_at' => now(),
    ]);

    ModuleProgress::create([
        'workshop_id' => $workshop1->id,
        'module_id' => $mod1->id,
        'teacher_id' => $teacher->id,
        'user_id' => $teacher->id,
        'progress' => 100,
        'status' => 'completed',
    ]);

    $response = $this->actingAs($teacher)->get(route('teacher.learning.module', ['workshop' => $workshop1->id, 'module' => $mod2->id]));

    $response->assertOk();
    $response->assertSee('Evacuation Drills');
});

test('assessment service evaluates score and creates a pending certificate on passing', function () {
    Notification::fake();
    User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);
    $teacher = User::factory()->create([
        'role' => 'teacher',
        'email_verified_at' => now(),
    ]);

    $workshop = Workshop::where('title', 'like', '%Earthquake%')->first();
    $assessment = $workshop->assessment;

    foreach ($workshop->ordered_modules->where('is_required', true) as $module) {
        if ((int) $module->id === (int) $assessment->module_id) {
            continue;
        }

        ModuleProgress::updateOrCreate(
            [
                'workshop_id' => $workshop->id,
                'module_id' => $module->id,
                'teacher_id' => $teacher->id,
            ],
            [
                'user_id' => $teacher->id,
                'progress' => 100,
                'status' => 'completed',
                'completed_at' => now(),
            ]
        );
    }

    WorkshopTeacher::create([
        'workshop_id' => $workshop->id,
        'teacher_id' => $teacher->id,
        'status' => 'registered',
        'joined_at' => now(),
    ]);

    // Build answers with correct choice for every question
    $answers = [];
    foreach ($assessment->questions as $question) {
        $correctChoice = $question->choices->firstWhere('is_correct', true);
        $answers[$question->id] = $correctChoice ? $correctChoice->id : null;
    }

    $service = app(AssessmentEvaluationService::class);
    $result = $service->evaluateAttempt($teacher, $assessment, $answers);

    expect($result['passed'])->toBeTrue();
    expect($result['percentage'])->toBeGreaterThanOrEqual(80.00);
    expect($result['certification'])->not->toBeNull();
    expect($result['certification']->certificate_number)->toStartWith('BRD-');
    expect($result['certification']->status)->toBe('pending');

    $this->assertDatabaseHas('certifications', [
        'teacher_id' => $teacher->id,
        'workshop_id' => $workshop->id,
        'status' => 'pending',
    ]);
    Notification::assertSentTo(
        User::whereIn('role', ['trainer', 'admin'])->get(),
        CertificatePendingApproval::class
    );
});

test('classroom package is locked for uncertified teacher and accessible for certified teacher', function () {
    $certifiedTeacher = User::where('email', 'certified.teacher@example.com')->first();
    $uncertifiedTeacher = User::factory()->create(['role' => 'teacher', 'email_verified_at' => now()]);

    $workshop2 = Workshop::where('title', 'like', '%Typhoon%')->first();
    $package = $workshop2->classroomPackage;

    // Uncertified teacher should be forbidden
    $forbiddenResponse = $this->actingAs($uncertifiedTeacher)->get(route('teacher.packages.show', ['package' => $package->id]));
    $forbiddenResponse->assertForbidden();

    // Certified teacher (already seeded with certification for workshop 2) should have access
    $okResponse = $this->actingAs($certifiedTeacher)->get(route('teacher.packages.show', ['package' => $package->id]));
    $okResponse->assertOk();
    $okResponse->assertSee('Workshop Repository');
});

test('classroom implementation service dynamically calculates student aggregates', function () {
    $service = app(ClassroomImplementationService::class);
    $teacher = User::where('email', 'certified.teacher@example.com')->first();
    $workshop2 = Workshop::where('title', 'like', '%Typhoon%')->first();
    $package = $workshop2->classroomPackage;

    $data = [
        'workshop_id' => $workshop2->id,
        'classroom_package_id' => $package->id,
        'school_name' => 'St. Jude High School',
        'school_id' => 'SCH-998877',
        'grade_level' => 'Grade 8',
        'section_name' => 'Rizal',
        'students_participated' => 3, // Will be recalculated by service
        'implementation_date' => now()->toDateString(),
        'teacher_reflection' => 'Great engagement during evacuation drill simulations.',
        'remarks' => 'Rainy weather required indoor path adjustment.',
        'students' => [
            ['student_identifier' => 'STU-001', 'score' => 88.00, 'max_score' => 100],
            ['student_identifier' => 'STU-002', 'score' => 92.00, 'max_score' => 100],
            ['student_identifier' => 'STU-003', 'score' => 60.00, 'max_score' => 100],
        ],
    ];

    $implementation = $service->recordImplementation($teacher, $data);

    // 2 passed (>=70), 1 failed (<70), average = (88+92+60)/3 = 80.00
    expect($implementation->total_students_reached)->toBe(3);
    expect($implementation->students_completed)->toBe(3);
    expect($implementation->students_passed)->toBe(2);
    expect($implementation->students_failed)->toBe(1);
    expect((float) $implementation->class_average_percentage)->toBe(80.00);

    $this->assertDatabaseHas('classroom_implementations', [
        'id' => $implementation->id,
        'students_passed' => 2,
        'students_failed' => 1,
    ]);

    $this->assertDatabaseCount('student_results', 11); // 8 from seeder + 3 new
});

test('all teacher blade views render successfully with status 200', function () {
    $teacher = User::where('email', 'certified.teacher@example.com')->first();
    $workshop1 = Workshop::where('title', 'like', '%Earthquake%')->first();
    $workshop2 = Workshop::where('title', 'like', '%Typhoon%')->first();
    $package2 = $workshop2->classroomPackage;
    $attempt = AssessmentAttempt::where('teacher_id', $teacher->id)->first();
    $implementation = ClassroomImplementation::where('teacher_id', $teacher->id)->first();

    // 1. Dashboard
    $this->actingAs($teacher)->get(route('teacher.dashboard'))->assertOk();

    // 2. Workshops Index & My Workshops
    $this->actingAs($teacher)->get(route('teacher.workshops.index'))->assertOk();
    $this->actingAs($teacher)->get(route('teacher.workshops.my'))->assertOk();

    // 3. Workshop Show
    $this->actingAs($teacher)->get(route('teacher.workshops.show', ['workshop' => $workshop1->id]))->assertOk();

    // 4. Module Show (workshop 2 module 1 is enrolled for certified teacher)
    $mod1 = $workshop2->course->modules->where('sequence', 1)->first();
    $this->actingAs($teacher)->get(route('teacher.learning.module', ['workshop' => $workshop2->id, 'module' => $mod1->id]))->assertOk();

    // 5. Assessment Result
    $this->actingAs($teacher)->get(route('teacher.assessments.result', ['workshop' => $workshop2->id, 'attempt' => $attempt->id]))->assertOk();

    // 6. Certificate View
    $this->actingAs($teacher)->get(route('teacher.assessments.certificate', ['workshop' => $workshop2->id]))->assertOk();

    // 7. Packages Index & Show
    $this->actingAs($teacher)->get(route('teacher.packages.index'))->assertOk();
    $this->actingAs($teacher)->get(route('teacher.packages.show', ['package' => $package2->id]))->assertOk();

    // 8. Implementation Index, Create & Show
    $this->actingAs($teacher)->get(route('teacher.implementations.index'))->assertOk();
    $this->actingAs($teacher)->get(route('teacher.implementations.create', ['package' => $package2->id]))->assertOk();
    $this->actingAs($teacher)->get(route('teacher.implementations.show', ['implementation' => $implementation->id]))->assertOk();

    // 9. Personal Reports Dashboard
    $this->actingAs($teacher)->get(route('teacher.reports.index'))->assertOk();
});

test('personal reports dashboard renders with all four pillars and computed metrics', function () {
    $teacher = User::where('email', 'certified.teacher@example.com')->first();

    $response = $this->actingAs($teacher)->get(route('teacher.reports.index'));

    $response->assertOk();
    $response->assertSee('My Training Progress');
    $response->assertSee('My Assessments');
    $response->assertSee('My Certificates');
    $response->assertSee('My Classroom Implementations');
    $response->assertSee('Class Average');
    $response->assertSee('Students Reached');
});

test('teacher interacting with material advances progress but requires the module quiz to complete', function () {
    $teacher = User::factory()->create(['role' => 'teacher', 'email_verified_at' => now()]);
    $workshop1 = Workshop::where('title', 'like', '%Earthquake%')->first();
    $mod1 = $workshop1->course->modules->where('sequence', 1)->first();
    $mod2 = $workshop1->course->modules->where('sequence', 2)->first();
    $material = $mod2->materials->first();

    WorkshopTeacher::create([
        'workshop_id' => $workshop1->id,
        'teacher_id' => $teacher->id,
        'user_id' => $teacher->id,
        'status' => 'registered',
        'joined_at' => now(),
    ]);

    ModuleProgress::create([
        'workshop_id' => $workshop1->id,
        'module_id' => $mod1->id,
        'teacher_id' => $teacher->id,
        'user_id' => $teacher->id,
        'progress' => 100,
        'status' => 'completed',
    ]);

    $response = $this->actingAs($teacher)->post(route('teacher.learning.material.interact', [
        'workshop' => $workshop1->id,
        'module' => $mod2->id,
        'material' => $material->id,
    ]));

    $response->assertSessionHas('success');

    $this->assertDatabaseHas('module_progress', [
        'workshop_id' => $workshop1->id,
        'module_id' => $mod2->id,
        'user_id' => $teacher->id,
        'progress' => 75,
        'status' => 'in_progress',
    ]);

    $this->actingAs($teacher)
        ->post(route('teacher.learning.module.complete', [$workshop1, $mod2]))
        ->assertRedirect(route('teacher.learning.module', [$workshop1, $mod2]))
        ->assertSessionHas('warning');
});
