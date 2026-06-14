<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mr-3">{{ __('Edit Kriteria') }}</h2>
        </div>
    </x-slot>

    <div class="page-stack">
        <div>
            @if (session('status'))
                <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
            @endif

            <div class="page-card">
                <form method="POST" action="{{ route('admin.criteria.update', $criterion) }}">
                    @method('PUT')
                    @include('admin.criteria._form', ['submit' => __('Perbarui')])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
