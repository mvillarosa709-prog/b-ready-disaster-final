<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->seed();
});

test('public registration is disabled and redirects to login', function () {
    $response = $this->get('/register');
    $response->assertRedirect('/login');

    $postResponse = $this->post('/register', [
        'name' => 'Stranger',
        'email' => 'stranger@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);
    $postResponse->assertRedirect('/login');
});

test('login page displays clean login form and does not show public register link or hardcoded demo credentials', function () {
    $response = $this->get('/login');
    $response->assertOk();
    $response->assertSee('Log in');
    $response->assertDontSee('href="http://localhost/register"', false);
    $response->assertDontSee('Demo Portal Credentials');
    $response->assertDontSee('adminbready@gmail.com');
});

test('super admin can log in with new credentials and is directed to admin dashboard', function () {
    $admin = User::where('email', 'adminbready@gmail.com')->firstOrFail();
    expect($admin->role)->toBe('admin');
    expect($admin->isAdmin())->toBeTrue();
    expect($admin->hasPermission('workshops.manage'))->toBeTrue();
    expect($admin->hasPermission('users.manage'))->toBeTrue();

    $response = $this->post('/login', [
        'email' => 'adminbready@gmail.com',
        'password' => 'adminbready123!',
    ]);

    $response->assertRedirect(route('admin.dashboard', absolute: false));
    $this->assertAuthenticatedAs($admin);

    $dashboardResponse = $this->actingAs($admin)->get('/admin/dashboard');
    $dashboardResponse->assertOk();
    $dashboardResponse->assertSee('SUPER ADMIN CONTROL CONSOLE');
    $dashboardResponse->assertSee('System Administration Overview');
});

test('trainer can log in with new credentials and is directed to trainer dashboard', function () {
    $trainer = User::where('email', 'trainerbready@gmail.com')->firstOrFail();
    expect($trainer->role)->toBe('trainer');
    expect($trainer->hasPermission('workshops.manage'))->toBeTrue();
    expect($trainer->hasPermission('courses.manage'))->toBeTrue();

    $response = $this->post('/login', [
        'email' => 'trainerbready@gmail.com',
        'password' => 'trainerbready123!',
    ]);

    $response->assertRedirect(route('trainer.dashboard', absolute: false));
    $this->assertAuthenticatedAs($trainer);
});

test('teacher can log in with new credentials and is directed to teacher dashboard', function () {
    $teacher = User::where('email', 'teacherbready@gmail.com')->firstOrFail();
    expect($teacher->role)->toBe('teacher');
    expect($teacher->hasPermission('packages.access'))->toBeTrue();

    $response = $this->post('/login', [
        'email' => 'teacherbready@gmail.com',
        'password' => 'teacherbready123!',
    ]);

    $response->assertRedirect(route('teacher.dashboard', absolute: false));
    $this->assertAuthenticatedAs($teacher);
});

test('regular users cannot access admin portal', function () {
    $trainer = User::where('email', 'trainerbready@gmail.com')->firstOrFail();
    $teacher = User::where('email', 'teacherbready@gmail.com')->firstOrFail();

    $this->actingAs($trainer)->get(route('admin.dashboard'))->assertForbidden();
    $this->actingAs($trainer)->get(route('admin.users.index'))->assertForbidden();

    $this->actingAs($teacher)->get(route('admin.dashboard'))->assertForbidden();
    $this->actingAs($teacher)->get(route('admin.users.index'))->assertForbidden();
});

test('super admin can manage users, assign roles, and grant permissions', function () {
    $admin = User::where('email', 'adminbready@gmail.com')->firstOrFail();

    // 1. Visit users list
    $this->actingAs($admin)->get(route('admin.users.index'))
        ->assertOk()
        ->assertSee('System User Directory')
        ->assertSee('adminbready@gmail.com');

    // 2. Create a new user with specific permissions
    $postResponse = $this->actingAs($admin)->post(route('admin.users.store'), [
        'name' => 'Custom Trainer',
        'email' => 'custom.trainer@example.com',
        'password' => 'SecretPassword123!',
        'role' => 'trainer',
        'is_active' => '1',
        'permissions' => ['workshops.manage', 'reports.view'],
    ]);

    $postResponse->assertRedirect(route('admin.users.index'));
    $postResponse->assertSessionHas('new_user_provisioned');

    $createdUser = User::where('email', 'custom.trainer@example.com')->firstOrFail();
    expect($createdUser->role)->toBe('trainer');
    expect($createdUser->must_change_password)->toBeTrue();
    expect($createdUser->hasPermission('workshops.manage'))->toBeTrue();
    expect($createdUser->hasPermission('reports.view'))->toBeTrue();
    expect($createdUser->hasPermission('courses.manage'))->toBeFalse(); // Not granted in custom permissions

    // 3. Edit user permissions
    $updateResponse = $this->actingAs($admin)->put(route('admin.users.update', $createdUser), [
        'name' => 'Custom Trainer Updated',
        'email' => 'custom.trainer@example.com',
        'role' => 'trainer',
        'is_active' => '1',
        'permissions' => ['workshops.manage', 'courses.manage', 'reports.view'],
    ]);

    $updateResponse->assertRedirect(route('admin.users.index'));
    $createdUser->refresh();
    expect($createdUser->hasPermission('courses.manage'))->toBeTrue();

    // 4. Toggle account status
    $this->actingAs($admin)->patch(route('admin.users.toggle-status', $createdUser))->assertRedirect();
    $createdUser->refresh();
    expect($createdUser->is_active)->toBeFalse();

    // 5. Delete user
    $this->actingAs($admin)->delete(route('admin.users.destroy', $createdUser))
        ->assertRedirect(route('admin.users.index'));

    expect(User::where('email', 'custom.trainer@example.com')->exists())->toBeFalse();
});

test('super admin can view permissions matrix and platform oversight pages', function () {
    $admin = User::where('email', 'adminbready@gmail.com')->firstOrFail();

    $this->actingAs($admin)->get(route('admin.permissions.index'))
        ->assertRedirect(route('admin.users.index'));

    $this->actingAs($admin)->get(route('admin.workshops.index'))
        ->assertOk()
        ->assertSee('Workshops');

    $this->actingAs($admin)->get(route('admin.courses.index'))
        ->assertOk()
        ->assertSee('Courses');

    $this->actingAs($admin)->get(route('admin.reports.index'))
        ->assertOk()
        ->assertSee('Analytics');
});

test('super admin visiting profile page retains super admin portal layout and navigation', function () {
    $admin = User::where('email', 'adminbready@gmail.com')->firstOrFail();

    $response = $this->actingAs($admin)->get(route('profile.edit'));
    $response->assertOk();
    $response->assertSee('Super Admin Portal');
    $response->assertSee('Super Admin Workspace');
    $response->assertSeeText('Users & Roles');
    $response->assertDontSee('B-READY Trainer Workspace');
});

test('teacher completing workshop assessment automatically receives approved certificate without trainer approval', function () {
    $teacher = \App\Models\User::where('email', 'teacherbready@gmail.com')->firstOrFail();
    $workshop = \App\Models\Workshop::where('title', 'like', '%Earthquake%')->firstOrFail();
    $assessment = $workshop->assessment;

    foreach ($workshop->ordered_modules->where('is_required', true) as $module) {
        if ((int) $module->id === (int) $assessment->module_id) {
            continue;
        }

        \App\Models\ModuleProgress::updateOrCreate(
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

    $answers = [];
    foreach ($assessment->questions as $question) {
        $correctChoice = $question->choices->firstWhere('is_correct', true);
        $answers[$question->id] = $correctChoice ? $correctChoice->id : null;
    }

    $service = app(\App\Services\AssessmentEvaluationService::class);
    $result = $service->evaluateAttempt($teacher, $assessment, $answers);

    expect($result['passed'])->toBeTrue();
    expect($result['certification'])->not->toBeNull();
    // Certificate is automatically approved without needing trainer permission
    expect($result['certification']->status)->toBe('approved');
    expect($result['certification']->approved_at)->not->toBeNull();

    // Teacher can immediately view and download the certificate
    $this->actingAs($teacher)
        ->get(route('teacher.assessments.certificate', $workshop))
        ->assertOk()
        ->assertSee('Approved & Signed')
        ->assertDontSee('Naghihintay pa ng beripikasyon');
});

test('newly provisioned teacher receives email invitation and is required to change password upon first login', function () {
    \Illuminate\Support\Facades\Mail::fake();

    $admin = User::where('email', 'adminbready@gmail.com')->firstOrFail();

    $response = $this->actingAs($admin)->post(route('admin.users.store'), [
        'name' => 'Maria Clara',
        'email' => 'mariaclara@gmail.com',
        'password' => 'TempPassword123!',
        'role' => 'teacher',
        'is_active' => '1',
    ]);

    $response->assertRedirect(route('admin.users.index'));
    $response->assertSessionHas('new_user_provisioned');

    \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\UserInvitationMail::class, function ($mail) {
        return $mail->hasTo('mariaclara@gmail.com') && $mail->plainPassword === 'TempPassword123!';
    });

    $newTeacher = User::where('email', 'mariaclara@gmail.com')->firstOrFail();
    expect($newTeacher->must_change_password)->toBeTrue();

    // Teacher logs in with temporary password
    $this->post('/logout');
    $loginResponse = $this->post('/login', [
        'email' => 'mariaclara@gmail.com',
        'password' => 'TempPassword123!',
    ]);

    $loginResponse->assertRedirect(route('teacher.dashboard', absolute: false));
    $this->assertAuthenticatedAs($newTeacher);

    // Visiting teacher dashboard redirects to change-password because must_change_password is true
    $dashboardAttempt = $this->actingAs($newTeacher)->get(route('teacher.dashboard'));
    $dashboardAttempt->assertRedirect(route('password.change'));

    // Visiting change-password form renders correctly
    $changeForm = $this->actingAs($newTeacher)->get(route('password.change'));
    $changeForm->assertOk();
    $changeForm->assertSee('Set your new password');

    // Submit new permanent password
    $updatePasswordResponse = $this->actingAs($newTeacher)->put(route('password.update'), [
        'current_password' => 'TempPassword123!',
        'password' => 'NewPermanentPassword99!',
        'password_confirmation' => 'NewPermanentPassword99!',
    ]);

    $updatePasswordResponse->assertRedirect(route('teacher.dashboard'));
    expect($newTeacher->fresh()->must_change_password)->toBeFalse();

    // Now teacher can access their dashboard without redirect
    $finalDashboard = $this->actingAs($newTeacher->fresh())->get(route('teacher.dashboard'));
    $finalDashboard->assertOk();
    $finalDashboard->assertSee('Welcome back, Maria Clara!');
});
