<?php

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentAttempt;
use App\Models\ModuleProgress;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopTeacher;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->seed();
});

test('passing a module quiz completes the module and redirects to the next module', function () {
    $workshop = Workshop::where('title', 'like', '%Earthquake%')->firstOrFail();
    $modules = $workshop->ordered_modules->sortBy('sequence')->values();
    $module = $modules->first();
    $nextModule = $modules->get(1);
    $assessment = $workshop->assessment;
    $assessment->update(['module_id' => $module->id]);
    $teacher = User::factory()->create(['role' => 'teacher']);

    WorkshopTeacher::create([
        'workshop_id' => $workshop->id,
        'teacher_id' => $teacher->id,
        'status' => 'registered',
        'joined_at' => now(),
    ]);

    $answers = [];
    foreach ($assessment->questions as $question) {
        $answers[$question->id] = (string) $question->choices->firstWhere('is_correct', true)->id;
    }

    $this->actingAs($teacher)
        ->get(route('teacher.learning.module', [$workshop, $module]))
        ->assertOk()
        ->assertSee('Module Quiz')
        ->assertSee('Submit Quiz');

    $response = $this->actingAs($teacher)->post(
        route('teacher.learning.module.assessment.submit', [$workshop, $module]),
        ['answers' => $answers]
    );

    $response->assertRedirect(route('teacher.learning.module', [$workshop, $nextModule]));
    $response->assertSessionHas('module_quiz_result.passed', true);
    $this->assertDatabaseHas('module_progress', [
        'workshop_id' => $workshop->id,
        'module_id' => $module->id,
        'teacher_id' => $teacher->id,
        'status' => 'completed',
    ]);
    $this->assertDatabaseHas('module_progress', [
        'workshop_id' => $workshop->id,
        'module_id' => $nextModule->id,
        'teacher_id' => $teacher->id,
        'status' => 'not_started',
    ]);

    $this->get(route('teacher.learning.module', [$workshop, $nextModule]))
        ->assertOk()
        ->assertSee('Quiz passed')
        ->assertSeeText('Score 100 / 100 points.')
        ->assertSee('is now unlocked');

    $attempt = AssessmentAttempt::latest('id')->firstOrFail();
    $this->get(route('teacher.learning.module', [$workshop, $module]))
        ->assertOk()
        ->assertSee('Your latest quiz result')
        ->assertSeeText('Score 100.00 / 100.00 points')
        ->assertSee('Passed');

    $this->get(route('teacher.assessments.result', [$workshop, $attempt]))
        ->assertOk()
        ->assertSeeText('Score:')
        ->assertDontSee('Question Breakdown')
        ->assertDontSee('Your Answer:');

    $this->post(
        route('teacher.learning.module.assessment.submit', [$workshop, $module]),
        ['answers' => $answers]
    )->assertForbidden();
});

test('failing a module quiz keeps the next module locked and allows a retake', function () {
    $workshop = Workshop::where('title', 'like', '%Earthquake%')->firstOrFail();
    $modules = $workshop->ordered_modules->sortBy('sequence')->values();
    $module = $modules->get(1);
    $nextModule = $modules->get(2);
    $assessment = $workshop->assessment;
    $assessment->update(['module_id' => $module->id]);
    $teacher = User::factory()->create(['role' => 'teacher']);

    WorkshopTeacher::create([
        'workshop_id' => $workshop->id,
        'teacher_id' => $teacher->id,
        'status' => 'registered',
        'joined_at' => now(),
    ]);

    ModuleProgress::create([
        'workshop_id' => $workshop->id,
        'module_id' => $modules->first()->id,
        'teacher_id' => $teacher->id,
        'user_id' => $teacher->id,
        'progress' => 100,
        'status' => 'completed',
    ]);

    $material = $module->materials()->firstOrFail();
    $this->actingAs($teacher)
        ->post(route('teacher.learning.material.interact', [$workshop, $module, $material]))
        ->assertRedirect();

    $this->post(route('teacher.learning.module.complete', [$workshop, $module]))
        ->assertRedirect(route('teacher.learning.module', [$workshop, $module]))
        ->assertSessionHas('warning');

    $answers = [];
    foreach ($assessment->questions as $question) {
        $answers[$question->id] = (string) $question->choices->firstWhere('is_correct', false)->id;
    }

    $this->actingAs($teacher)
        ->post(route('teacher.learning.module.assessment.submit', [$workshop, $module]), ['answers' => $answers])
        ->assertRedirect(route('teacher.learning.module', [$workshop, $module]))
        ->assertSessionHas('module_quiz_result.passed', false);

    $this->assertDatabaseHas('module_progress', [
        'workshop_id' => $workshop->id,
        'module_id' => $module->id,
        'teacher_id' => $teacher->id,
        'status' => 'in_progress',
    ]);

    $this->get(route('teacher.learning.module', [$workshop, $nextModule]))
        ->assertForbidden();

    $this->get(route('teacher.learning.module', [$workshop, $module]))
        ->assertOk()
        ->assertSee('Your latest quiz result')
        ->assertSeeText('Score 0.00 / 100.00 points')
        ->assertSee('Failed')
        ->assertSee('Retake Quiz');
});

test('trainer dashboard requires authentication', function () {
    $this->get(route('trainer.dashboard'))
        ->assertRedirect(route('login'));
});

test('temporary password blocks trainer dashboard until password changes', function () {
    $trainer = User::factory()->create([
        'role' => 'trainer',
        'password' => Hash::make('temporary-password'),
    ]);
    $trainer->must_change_password = true;
    $trainer->save();

    $this->actingAs($trainer)
        ->get(route('trainer.dashboard'))
        ->assertRedirect(route('trainer.password.change'));

    $this->get(route('trainer.courses.index'))
        ->assertRedirect(route('trainer.password.change'));

    $this->get(route('trainer.password.change'))->assertOk();

    $this->put(route('trainer.password.update'), [
        'current_password' => 'temporary-password',
        'password' => 'new-secure-password',
        'password_confirmation' => 'new-secure-password',
    ])->assertRedirect(route('trainer.dashboard'));

    $trainer->refresh();
    expect($trainer->must_change_password)->toBeFalse();
    expect(Hash::check('new-secure-password', $trainer->password))->toBeTrue();

    $this->get(route('trainer.dashboard'))->assertOk();
});

test('trainer reports show only owned module quiz answers', function () {
    $trainer = User::where('role', 'trainer')->firstOrFail();
    $assessment = Assessment::whereNotNull('module_id')
        ->whereHas('module.course', fn ($query) => $query->where('trainer_id', $trainer->id))
        ->firstOrFail();
    $teacher = User::where('role', 'teacher')->firstOrFail();
    $question = $assessment->questions()->firstOrFail();
    $correctChoice = $question->choices->firstWhere('is_correct', true);
    $attempt = AssessmentAttempt::create([
        'assessment_id' => $assessment->id,
        'workshop_id' => $assessment->workshop_id,
        'teacher_id' => $teacher->id,
        'attempt_number' => 1,
        'score' => $question->points,
        'percentage' => 100,
        'result' => 'passed',
        'started_at' => now(),
        'submitted_at' => now(),
    ]);
    AssessmentAnswer::create([
        'attempt_id' => $attempt->id,
        'question_id' => $question->id,
        'choice_id' => $correctChoice->id,
        'is_correct' => true,
        'points_earned' => $question->points,
    ]);

    $otherTrainer = User::factory()->create(['role' => 'trainer']);
    $assessment->module->course->update(['trainer_id' => $otherTrainer->id]);

    $this->actingAs($trainer)
        ->get(route('trainer.reports'))
        ->assertOk()
        ->assertDontSee($assessment->module->title);

    $this->actingAs($otherTrainer)
        ->get(route('trainer.reports'))
        ->assertOk()
        ->assertSee($assessment->module->title)
        ->assertSee('Review submitted answers')
        ->assertSee($correctChoice->choice_text);
});

test('standalone assessment bank is removed from trainer portal', function () {
    $trainer = User::where('role', 'trainer')->firstOrFail();

    $this->actingAs($trainer)
        ->get(route('trainer.dashboard'))
        ->assertOk()
        ->assertSee('b-ready-logo.png')
        ->assertDontSee('Assessment Bank & Quiz')
        ->assertDontSee('Upload CSV Questions');

    $this->actingAs($trainer)
        ->get('/trainer/assessments')
        ->assertNotFound();
});
