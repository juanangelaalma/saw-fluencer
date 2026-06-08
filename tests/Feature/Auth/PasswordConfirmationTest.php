<?php

use App\Models\User;

test('password confirmation screen is not available', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/confirm-password')->assertNotFound();
});

test('password confirmation post is not available', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/confirm-password', [
        'password' => 'password',
    ])->assertNotFound();
});
