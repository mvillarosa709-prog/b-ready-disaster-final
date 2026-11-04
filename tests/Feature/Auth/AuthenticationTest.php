<?php

use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertOk()
        ->assertSee('images/b-ready-logo.png')
        ->assertSee('Welcome back');
});

test('homepage sends guests directly to login', function () {
    $this->get('/')
        ->assertRedirect(route('login'));
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create(['role' => 'teacher']);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('teacher.dashboard', absolute: false));
});

test('trainer login goes directly to trainer dashboard', function () {
    $trainer = User::factory()->create(['role' => 'trainer']);

    $this->post('/login', [
        'email' => $trainer->email,
        'password' => 'password',
    ])->assertRedirect(route('trainer.dashboard', absolute: false));
});

test('homepage sends authenticated users to the dashboard for their role', function () {
    $teacher = User::factory()->create(['role' => 'teacher']);
    $this->actingAs($teacher)
        ->get('/')
        ->assertRedirect(route('teacher.dashboard'));

    $trainer = User::factory()->create(['role' => 'trainer']);
    $this->actingAs($trainer)
        ->get('/')
        ->assertRedirect(route('trainer.dashboard'));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect(route('login'));
});
