<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Tambah Pengguna') }}</h2>
    </x-slot>

    <div class="page-stack">
        <div>
            <div class="page-card">
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @include('admin.users._form', ['submit' => __('Simpan')])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
