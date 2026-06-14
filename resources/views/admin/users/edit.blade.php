<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Pengguna') }}</h2>
    </x-slot>

    <div class="page-stack">
        <div>
            @if (session('status'))
                <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
            @endif

            <div class="page-card">
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @method('PUT')
                    @include('admin.users._form', ['submit' => __('Perbarui')])
                </form>
            </div>

            @if (! auth()->user()->is($user) && $user->is_active)
                <div class="mt-6 page-card">
                    <h3 class="text-lg font-medium text-gray-900">Nonaktifkan Pengguna</h3>
                    <p class="mt-1 text-sm text-gray-600">Pengguna nonaktif tidak dapat login. Data pengguna tetap tersimpan.</p>
                    <form method="POST" action="{{ route('admin.users.deactivate', $user) }}" class="mt-4">
                        @csrf
                        @method('PATCH')
                        <x-danger-button>{{ __('Nonaktifkan') }}</x-danger-button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
