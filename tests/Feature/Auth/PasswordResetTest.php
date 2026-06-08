<?php

test('password reset request screen is not available', function () {
    $this->get('/forgot-password')->assertNotFound();
});

test('public password reset request is not available', function () {
    $this->post('/forgot-password', ['username' => 'admin'])->assertNotFound();
});

test('password reset screen is not available', function () {
    $this->get('/reset-password/token-value')->assertNotFound();
});

test('public password reset post is not available', function () {
    $this->post('/reset-password', [
        'token' => 'token-value',
        'username' => 'admin',
        'password' => 'password',
    ])->assertNotFound();
});
