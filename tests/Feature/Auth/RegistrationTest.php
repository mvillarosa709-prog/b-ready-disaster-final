<?php

test('registration screen redirects to login because self-registration is disabled', function () {
    $response = $this->get('/register');

    $response->assertRedirect('/login');
});

test('new user self-registration attempts redirect to login', function () {
    $response = $this->post('/register', [
        'name' => 'Self Registering User',
        'email' => 'selfreg@example.com',
        'password' => 'password123!',
        'password_confirmation' => 'password123!',
        'role' => 'teacher',
    ]);

    $response->assertRedirect('/login');
    $this->assertGuest();
});
