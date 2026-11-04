<?php

it('redirects guests from the homepage to login', function () {
    $this->get('/')
        ->assertRedirect(route('login'));
});
