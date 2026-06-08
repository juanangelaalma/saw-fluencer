<?php

use Symfony\Component\Routing\Exception\RouteNotFoundException;

test('email verification screen is not available', function () {
    $this->get('/verify-email')->assertNotFound();
});

test('email verification route name is not registered', function () {
    expect(fn () => route('verification.verify', ['id' => 1, 'hash' => 'hash']))->toThrow(RouteNotFoundException::class);
});
