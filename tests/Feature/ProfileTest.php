<?php

use App\Models\User;

test('profile page is not available', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->get('/profile')
        ->assertNotFound();
});

test('profile update route is not available', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->patch('/profile', ['name' => 'Test User'])
        ->assertNotFound();
});

test('profile deletion route is not available', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->delete('/profile', ['password' => 'password'])
        ->assertNotFound();

    $this->assertNotNull($user->fresh());
});
