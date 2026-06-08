<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Hash;

it('creates valid users from factory defaults', function () {
    $user = User::factory()->create();

    expect($user->username)->not->toBeEmpty()
        ->and($user->role)->toBeIn([User::ROLE_ADMIN, User::ROLE_MANAJER])
        ->and($user->is_active)->toBeTrue();
});

it('seeds active admin from environment values', function () {
    putenv('ADMIN_NAME=Primary Admin');
    putenv('ADMIN_USERNAME=SeedAdmin');
    putenv('ADMIN_PASSWORD=secret-password');
    $_ENV['ADMIN_NAME'] = 'Primary Admin';
    $_ENV['ADMIN_USERNAME'] = 'SeedAdmin';
    $_ENV['ADMIN_PASSWORD'] = 'secret-password';
    $_SERVER['ADMIN_NAME'] = 'Primary Admin';
    $_SERVER['ADMIN_USERNAME'] = 'SeedAdmin';
    $_SERVER['ADMIN_PASSWORD'] = 'secret-password';

    $this->seed(DatabaseSeeder::class);

    $user = User::query()->where('username', 'seedadmin')->firstOrFail();

    expect($user->name)->toBe('Primary Admin')
        ->and($user->role)->toBe(User::ROLE_ADMIN)
        ->and($user->is_active)->toBeTrue()
        ->and(Hash::check('secret-password', $user->password))->toBeTrue();
});

it('rejects weak seed admin passwords', function () {
    putenv('ADMIN_NAME=Primary Admin');
    putenv('ADMIN_USERNAME=SeedAdmin');
    putenv('ADMIN_PASSWORD=short');
    $_ENV['ADMIN_NAME'] = 'Primary Admin';
    $_ENV['ADMIN_USERNAME'] = 'SeedAdmin';
    $_ENV['ADMIN_PASSWORD'] = 'short';
    $_SERVER['ADMIN_NAME'] = 'Primary Admin';
    $_SERVER['ADMIN_USERNAME'] = 'SeedAdmin';
    $_SERVER['ADMIN_PASSWORD'] = 'short';

    $this->seed(DatabaseSeeder::class);
})->throws(InvalidArgumentException::class, 'ADMIN_PASSWORD must be at least 8 characters.');
