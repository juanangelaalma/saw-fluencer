@csrf

<div class="grid gap-6 sm:grid-cols-2">
    <div>
        <x-input-label for="name" :value="__('Nama')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label for="username" :value="__('Username')" />
        <x-text-input id="username" name="username" type="text" class="mt-1 block w-full" :value="old('username', $user->username)" required />
        <x-input-error class="mt-2" :messages="$errors->get('username')" />
    </div>

    <div>
        <x-input-label for="password" :value="$user->exists ? __('Password Baru') : __('Password')" />
        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" :required="! $user->exists" autocomplete="new-password" />
        <x-input-error class="mt-2" :messages="$errors->get('password')" />
        @if ($user->exists)
            <p class="mt-2 text-sm text-gray-500">Kosongkan jika password tidak diubah.</p>
        @endif
    </div>

    <div>
        <x-input-label for="role" :value="__('Role')" />
        <select id="role" name="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required @disabled($user->exists && auth()->user()->is($user))>
            @foreach ($roles as $value => $label)
                <option value="{{ $value }}" @selected(old('role', $user->role) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @if ($user->exists && auth()->user()->is($user))
            <input type="hidden" name="role" value="{{ $user->role }}">
            <p class="mt-2 text-sm text-gray-500">Role akun sendiri tidak dapat diubah.</p>
        @endif
        <x-input-error class="mt-2" :messages="$errors->get('role')" />
    </div>

    @if ($user->exists)
        <div>
            <x-input-label for="is_active" :value="__('Status')" />
            <select id="is_active" name="is_active" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required @disabled(auth()->user()->is($user))>
                <option value="1" @selected((string) old('is_active', (int) $user->is_active) === '1')>Aktif</option>
                <option value="0" @selected((string) old('is_active', (int) $user->is_active) === '0')>Nonaktif</option>
            </select>
            @if (auth()->user()->is($user))
                <input type="hidden" name="is_active" value="1">
                <p class="mt-2 text-sm text-gray-500">Admin tidak dapat menonaktifkan akun sendiri.</p>
            @endif
            <x-input-error class="mt-2" :messages="$errors->get('is_active')" />
        </div>
    @endif
</div>

<div class="mt-6 flex items-center gap-3">
    <x-primary-button>{{ $submit }}</x-primary-button>
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
        {{ __('Batal') }}
    </a>
</div>
