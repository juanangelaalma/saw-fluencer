<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="mr-6">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight mr-3">{{ __('Manajemen Kriteria') }}</h2>
                <p class="mt-1 text-sm {{ $weightTotal <= 100 ? 'text-green-700' : 'text-red-700' }}">Total bobot: {{ $weightTotal }}% / maksimal 100%</p>
            </div>
            <a href="{{ route('admin.criteria.create') }}" class="btn btn-primary">
                {{ __('Tambah Kriteria') }}
            </a>
        </div>
    </x-slot>

    <div>
        <div>
            @if (session('status'))
                <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
            @endif

            @if (session('error'))
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">{{ session('error') }}</div>
            @endif

            <div class="card overflow-hidden p-0">
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Bobot</th>
                                <th>Jenis</th>
                                <th class="num">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($criteria as $criterion)
                                <tr>
                                    <td>{{ $criterion->code }}</td>
                                    <td>{{ $criterion->name }}</td>
                                    <td>{{ $criterion->weight }}%</td>
                                    <td>{{ $criterion->typeLabel() }}</td>
                                    <td>
                                        <div class="table-actions">
                                            <x-table-action-link :href="route('admin.criteria.sub-criteria.edit', $criterion)">Sub Kriteria</x-table-action-link>
                                            <x-table-action-link :href="route('admin.criteria.edit', $criterion)" variant="edit">Edit</x-table-action-link>
                                            <form method="POST" action="{{ route('admin.criteria.destroy', $criterion) }}">
                                                @csrf
                                                @method('DELETE')
                                                <x-table-action-button variant="delete">Hapus</x-table-action-button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-200 px-6 py-4">
                    {{ $criteria->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
