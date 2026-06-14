<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mr-3">{{ __('Tambah Kriteria') }}</h2>
        </div>
    </x-slot>

    <div class="page-stack">
        <div>
            <div class="page-card">
                <form method="POST" action="{{ route('admin.criteria.store') }}">
                    @include('admin.criteria._form', ['submit' => __('Simpan')])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
