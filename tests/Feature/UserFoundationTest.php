<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\KasiUserSeeder;
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

it('seeds an active kasi user from environment values', function () {
    foreach ([
        'ADMIN_NAME' => 'Primary Admin',
        'ADMIN_USERNAME' => 'SeedAdmin',
        'ADMIN_PASSWORD' => 'secret-password',
        'KASI_NAME' => 'Kasi Pertama',
        'KASI_USERNAME' => 'SeedKasi',
        'KASI_PASSWORD' => 'secret-password',
    ] as $key => $value) {
        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }

    $this->seed(DatabaseSeeder::class);

    $user = User::query()->where('username', 'seedkasi')->firstOrFail();

    expect($user->name)->toBe('Kasi Pertama')
        ->and($user->role)->toBe(User::ROLE_KASI)
        ->and($user->roleLabel())->toBe('Kasi/SPV')
        ->and($user->is_active)->toBeTrue()
        ->and(Hash::check('secret-password', $user->password))->toBeTrue()
        ->and($user->canViewInfluencers())->toBeTrue()
        ->and($user->canManageInfluencers())->toBeFalse();
});

it('keeps the seeded kasi user updated when the seeder runs again', function () {
    foreach ([
        'ADMIN_NAME' => 'Primary Admin',
        'ADMIN_USERNAME' => 'SeedAdmin',
        'ADMIN_PASSWORD' => 'secret-password',
        'KASI_NAME' => 'Kasi Awal',
        'KASI_USERNAME' => 'SeedKasi',
        'KASI_PASSWORD' => 'secret-password',
    ] as $key => $value) {
        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }

    $this->seed(DatabaseSeeder::class);

    putenv('KASI_NAME=Kasi Diperbarui');
    $_ENV['KASI_NAME'] = 'Kasi Diperbarui';
    $_SERVER['KASI_NAME'] = 'Kasi Diperbarui';

    $this->seed(DatabaseSeeder::class);

    $users = User::query()->where('username', 'seedkasi')->get();

    expect($users)->toHaveCount(1)
        ->and($users->first()->name)->toBe('Kasi Diperbarui');
});

it('rejects weak seed kasi passwords', function () {
    foreach ([
        'ADMIN_NAME' => 'Primary Admin',
        'ADMIN_USERNAME' => 'SeedAdmin',
        'ADMIN_PASSWORD' => 'secret-password',
        'KASI_NAME' => 'Kasi Lemah',
        'KASI_USERNAME' => 'SeedKasi',
        'KASI_PASSWORD' => 'short',
    ] as $key => $value) {
        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }

    $this->seed(DatabaseSeeder::class);
})->throws(InvalidArgumentException::class, 'KASI_PASSWORD must be at least 8 characters.');

it('requires kasi environment values before seeding the kasi user', function () {
    foreach ([
        'ADMIN_NAME' => 'Primary Admin',
        'ADMIN_USERNAME' => 'SeedAdmin',
        'ADMIN_PASSWORD' => 'secret-password',
        'KASI_NAME' => null,
        'KASI_USERNAME' => null,
        'KASI_PASSWORD' => null,
    ] as $key => $value) {
        if ($value === null) {
            putenv($key);
            unset($_ENV[$key], $_SERVER[$key]);

            continue;
        }

        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }

    $this->seed(KasiUserSeeder::class);
})->throws(InvalidArgumentException::class, 'KASI_NAME must be set before seeding initial kasi user.');
