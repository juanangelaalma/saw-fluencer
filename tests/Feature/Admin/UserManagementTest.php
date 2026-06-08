<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('allows admin to view users', function () {
    $admin = User::factory()->admin()->create(['name' => 'Admin Satu', 'username' => 'admin_satu']);
    $manager = User::factory()->manajer()->inactive()->create(['name' => 'Manajer Satu', 'username' => 'manajer_satu']);

    $this->actingAs($admin)
        ->get(route('admin.users.index'))
        ->assertOk()
        ->assertSee('Admin Satu')
        ->assertSee('admin_satu')
        ->assertSee('Manajer Satu')
        ->assertSee('manajer_satu')
        ->assertSee('Nonaktif');
});

it('allows admin to create active user', function () {
    $admin = User::factory()->admin()->create(['username' => 'admin_alpha']);

    $this->actingAs($admin)
        ->post(route('admin.users.store'), [
            'name' => 'Manajer Baru',
            'username' => 'manajer_baru',
            'password' => 'secret123',
            'role' => User::ROLE_MANAJER,
        ])
        ->assertRedirect(route('admin.users.index'));

    $user = User::query()->where('username', 'manajer_baru')->firstOrFail();

    expect($user->name)->toBe('Manajer Baru')
        ->and($user->role)->toBe(User::ROLE_MANAJER)
        ->and($user->is_active)->toBeTrue()
        ->and(Hash::check('secret123', $user->password))->toBeTrue();
});

it('validates weak password and duplicate username on create', function () {
    $admin = User::factory()->admin()->create(['username' => 'admin_beta']);
    User::factory()->manajer()->create(['username' => 'existing_user']);

    $this->actingAs($admin)
        ->post(route('admin.users.store'), [
            'name' => 'Manajer Baru',
            'username' => 'existing_user',
            'password' => 'short',
            'role' => User::ROLE_MANAJER,
        ])
        ->assertSessionHasErrors(['username', 'password']);
});

it('updates user and hashes optional password when provided', function () {
    $admin = User::factory()->admin()->create(['username' => 'admin_gamma']);
    $manager = User::factory()->manajer()->create(['username' => 'old_user']);

    $this->actingAs($admin)
        ->put(route('admin.users.update', $manager), [
            'name' => 'Admin Baru',
            'username' => 'admin_baru',
            'password' => 'newsecret',
            'role' => User::ROLE_ADMIN,
            'is_active' => '1',
        ])
        ->assertRedirect(route('admin.users.edit', $manager));

    $manager->refresh();

    expect($manager->name)->toBe('Admin Baru')
        ->and($manager->username)->toBe('admin_baru')
        ->and($manager->role)->toBe(User::ROLE_ADMIN)
        ->and(Hash::check('newsecret', $manager->password))->toBeTrue();
});

it('keeps current password when edit password is blank', function () {
    $admin = User::factory()->admin()->create();
    $manager = User::factory()->manajer()->create(['username' => 'manager_alpha']);
    $password = $manager->password;

    $this->actingAs($admin)
        ->put(route('admin.users.update', $manager), [
            'name' => $manager->name,
            'username' => $manager->username,
            'password' => '',
            'role' => $manager->role,
            'is_active' => '1',
        ])
        ->assertSessionDoesntHaveErrors();

    expect($manager->refresh()->password)->toBe($password);
});

it('prevents admin from changing own role or deactivating self through update', function () {
    $admin = User::factory()->admin()->create(['username' => 'admin_self']);

    $this->actingAs($admin)
        ->put(route('admin.users.update', $admin), [
            'name' => $admin->name,
            'username' => $admin->username,
            'password' => '',
            'role' => User::ROLE_MANAJER,
            'is_active' => '0',
        ])
        ->assertSessionDoesntHaveErrors();

    expect($admin->refresh()->role)->toBe(User::ROLE_ADMIN)
        ->and($admin->is_active)->toBeTrue();
});

it('prevents admin from deactivating self through deactivate route', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->patch(route('admin.users.deactivate', $admin))
        ->assertStatus(422);

    expect($admin->refresh()->is_active)->toBeTrue();
});

it('deactivates other user without deleting', function () {
    $admin = User::factory()->admin()->create();
    $manager = User::factory()->manajer()->create();

    $this->actingAs($admin)
        ->patch(route('admin.users.deactivate', $manager))
        ->assertRedirect(route('admin.users.index'));

    expect($manager->refresh()->is_active)->toBeFalse();
    $this->assertDatabaseHas('users', ['id' => $manager->id]);
});

it('denies manajer access to user management routes', function (string $method, string $routeName) {
    $manager = User::factory()->manajer()->create();
    $target = User::factory()->admin()->create();

    $route = match ($routeName) {
        'admin.users.edit', 'admin.users.update', 'admin.users.deactivate' => route($routeName, $target),
        default => route($routeName),
    };

    $this->actingAs($manager)
        ->{$method}($route)
        ->assertForbidden();
})->with([
    ['get', 'admin.users.index'],
    ['get', 'admin.users.create'],
    ['post', 'admin.users.store'],
    ['get', 'admin.users.edit'],
    ['put', 'admin.users.update'],
    ['patch', 'admin.users.deactivate'],
]);
