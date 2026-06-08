<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use InvalidArgumentException;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(Hasher $hasher): void
    {
        $name = env('ADMIN_NAME');
        $username = env('ADMIN_USERNAME');
        $password = env('ADMIN_PASSWORD');

        if (! is_string($name) || trim($name) === '') {
            throw new InvalidArgumentException('ADMIN_NAME must be set before seeding initial admin.');
        }

        if (! is_string($username) || trim($username) === '') {
            throw new InvalidArgumentException('ADMIN_USERNAME must be set before seeding initial admin.');
        }

        if (! is_string($password) || Str::length($password) < 8) {
            throw new InvalidArgumentException('ADMIN_PASSWORD must be at least 8 characters.');
        }

        User::query()->updateOrCreate([
            'username' => Str::lower(trim($username)),
        ], [
            'name' => trim($name),
            'password' => $hasher->make($password),
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);
    }
}
