<?php

test('registration screen is not available', function () {
    $this->get('/register')->assertNotFound();
});

test('public registration post is not available', function () {
    $this->post('/register', [
        'name' => 'Test User',
        'username' => 'test_user',
        'password' => 'password',
    ])->assertNotFound();

    $this->assertGuest();
});
