<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

it('shows username login fields without public recovery links', function () {
    $response = $this->get('/login');

    $response
        ->assertOk()
        ->assertSee('Username')
        ->assertSee('Password')
        ->assertSee('name="username"', false)
        ->assertSee('name="password"', false)
        ->assertSee('name="remember"', false)
        ->assertSee('Setelah 5 percobaan gagal')
        ->assertDontSee('Forgot your password?')
        ->assertDontSee('name="role"', false);
});

it('hides public registration link from welcome page', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('SPK Influencer SAW')
        ->assertSee('Masuk ke Sistem')
        ->assertDontSee('Register')
        ->assertDontSee('Forgot your password?');
});

it('shows generic login guidance after invalid credentials', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'username' => $user->username,
        'password' => 'wrong-password',
    ]);

    $this->get('/login')
        ->assertOk()
        ->assertSee('Login belum berhasil.')
        ->assertSee('Username atau password salah.');
});

it('shows admin navigation with username role and user management link', function () {
    Route::get('/admin/users', fn () => response('ok'))->name('admin.users.index');
    Route::getRoutes()->refreshNameLookups();

    $user = User::factory()->admin()->create([
        'username' => 'adminuser',
    ]);

    $this->actingAs($user)
        ->get('/admin/dashboard')
        ->assertOk()
        ->assertSee('adminuser')
        ->assertSee('Admin')
        ->assertSee('Manajemen Pengguna')
        ->assertDontSee('Profile');
});

it('hides user management navigation for manajer', function () {
    Route::get('/admin/users', fn () => response('ok'))->name('admin.users.index');
    Route::getRoutes()->refreshNameLookups();

    $user = User::factory()->manajer()->create([
        'username' => 'manageruser',
    ]);

    $this->actingAs($user)
        ->get('/manager/dashboard')
        ->assertOk()
        ->assertSee('manageruser')
        ->assertSee('Manajer')
        ->assertDontSee('Manajemen Pengguna')
        ->assertDontSee('Profile');
});

it('renders role dashboard placeholders', function () {
    $admin = User::factory()->admin()->create();
    $manager = User::factory()->manajer()->create();

    $this->actingAs($admin)
        ->get('/admin/dashboard')
        ->assertOk()
        ->assertSee('Dashboard Admin')
        ->assertSee('Ringkasan Admin akan tersedia pada tahap berikutnya.');

    $this->actingAs($manager)
        ->get('/manager/dashboard')
        ->assertOk()
        ->assertSee('Dashboard Manajer')
        ->assertSee('Ringkasan Manajer akan tersedia pada tahap berikutnya.');
});
