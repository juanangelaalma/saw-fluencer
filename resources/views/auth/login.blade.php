<x-guest-layout>
    <div class="mb-8">
        <p class="eyebrow">Login</p>
        <h2 class="mt-3">Masuk</h2>
        <p class="hint mt-3">Masukkan username dan password. Jika gagal masuk, periksa kembali kredensial atau hubungi admin.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if ($errors->any())
        <div class="alert mb-5" role="alert" aria-live="polite">
            <p class="font-medium">Login belum berhasil.</p>
            <p class="mt-1">Username atau password salah. Setelah 5 percobaan gagal, akses login dikunci sementara selama 10 menit.</p>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="grid gap-5">
        @csrf

        <div class="field">
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input id="username" type="text" name="username" :value="old('username')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('username')" />
        </div>

        <div class="field">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <label for="remember_me" class="flex min-h-11 items-center gap-2 text-sm text-[var(--muted)]">
            <input id="remember_me" type="checkbox" class="h-4 w-4 rounded border-[var(--border)] text-[var(--accent)] focus:ring-[var(--accent)]" name="remember">
            <span>Ingat sesi masuk</span>
        </label>

        <div class="actions justify-end">
            <x-primary-button>
                Masuk
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
