<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('admin can authenticate with username and is redirected to admin dashboard', function () {
    $user = User::factory()->admin()->create();

    $response = $this->post('/login', [
        'username' => $user->username,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('admin.dashboard', absolute: false));
});

test('manajer can authenticate with username and is redirected to manager dashboard', function () {
    $user = User::factory()->manajer()->create();

    $response = $this->post('/login', [
        'username' => $user->username,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('manager.dashboard', absolute: false));
});

test('users can not authenticate with invalid password and see generic credential message', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'username' => $user->username,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors(['username' => 'Username atau password salah']);
});

test('login is locked for ten minutes after five failed attempts', function () {
    RateLimiter::clear('locked_user|127.0.0.1');

    User::factory()->create([
        'username' => 'locked_user',
        'password' => Hash::make('password'),
    ]);

    for ($attempt = 1; $attempt <= 5; $attempt++) {
        $this->post('/login', [
            'username' => 'LOCKED_USER',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors(['username' => 'Username atau password salah']);
    }

    $this->post('/login', [
        'username' => 'locked_user',
        'password' => 'password',
    ])->assertSessionHasErrors('username');

    expect(RateLimiter::availableIn('locked_user|127.0.0.1'))->toBeLessThanOrEqual(600)
        ->and(RateLimiter::availableIn('locked_user|127.0.0.1'))->toBeGreaterThan(0);

    $this->assertGuest();
});

test('inactive user can not authenticate', function () {
    $user = User::factory()->inactive()->create();

    $response = $this->post('/login', [
        'username' => $user->username,
        'password' => 'password',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors(['username' => 'Username atau password salah']);
});

test('inactive authenticated session is forced to logout', function () {
    $user = User::factory()->admin()->inactive()->create();

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertRedirect(route('login', absolute: false));

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
    $response->assertSessionMissing('auth.password_confirmed_at');
});
