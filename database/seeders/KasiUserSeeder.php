<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use InvalidArgumentException;

class KasiUserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(Hasher $hasher): void
    {
        $name = env('KASI_NAME');
        $username = env('KASI_USERNAME');
        $password = env('KASI_PASSWORD');

        if (! is_string($name) || trim($name) === '') {
            throw new InvalidArgumentException('KASI_NAME must be set before seeding initial kasi user.');
        }

        if (! is_string($username) || trim($username) === '') {
            throw new InvalidArgumentException('KASI_USERNAME must be set before seeding initial kasi user.');
        }

        if (! is_string($password) || Str::length($password) < 8) {
            throw new InvalidArgumentException('KASI_PASSWORD must be at least 8 characters.');
        }

        User::query()->updateOrCreate([
            'username' => Str::lower(trim($username)),
        ], [
            'name' => trim($name),
            'password' => $hasher->make($password),
            'role' => User::ROLE_KASI,
            'is_active' => true,
        ]);
    }
}
