<?php

use App\Models\User;

test('self-service password update is not available', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->put('/password', [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertNotFound();

    expect($user->refresh()->password)->not->toBeNull();
});
