<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mr-3">{{ __('Manajemen Pengguna') }}</h2>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                {{ __('Tambah Pengguna') }}
            </a>
        </div>
    </x-slot>

    <div>
        <div>
            @if (session('status'))
                <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
            @endif

            <div class="card overflow-hidden p-0">
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th class="num">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->roleLabel() }}</td>
                                    <td>
                                        <span class="inline-flex rounded-full border px-2 py-1 text-xs font-medium {{ $user->is_active ? 'border-green-200 bg-green-50 text-green-700' : 'border-red-200 bg-red-50 text-red-700' }}">
                                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="table-actions">
                                            <x-table-action-link :href="route('admin.users.edit', $user)" variant="edit">Edit</x-table-action-link>
                                            @if (! auth()->user()->is($user) && $user->is_active)
                                                <form method="POST" action="{{ route('admin.users.deactivate', $user) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <x-table-action-button variant="danger">Nonaktifkan</x-table-action-button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-200 px-6 py-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
