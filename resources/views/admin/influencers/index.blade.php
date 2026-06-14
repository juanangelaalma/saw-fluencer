<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mr-3">{{ __('Manajemen Influencer') }}</h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.influencers.import.create') }}" class="btn btn-secondary">{{ __('Import CSV') }}</a>
                <a href="{{ route('admin.influencers.create') }}" class="btn btn-primary">{{ __('Tambah Influencer') }}</a>
            </div>
        </div>
    </x-slot>

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
                            @foreach ($criteria as $criterion)
                                <th>{{ $criterion->name }}</th>
                            @endforeach
                            <th class="num">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($influencers as $influencer)
                            @php
                                $scores = $influencer->scores->keyBy('criterion_id');
                            @endphp
                            <tr>
                                <td>{{ $influencer->name }}</td>
                                <td>{{ $influencer->username }}</td>
                                @foreach ($criteria as $criterion)
                                    <td>{{ $scores->get($criterion->id)?->raw_value ?? '-' }}</td>
                                @endforeach
                                <td>
                                    <div class="table-actions">
                                        <x-table-action-link :href="route('admin.influencers.edit', $influencer)" variant="edit">Edit</x-table-action-link>
                                        <form method="POST" action="{{ route('admin.influencers.destroy', $influencer) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus influencer ini?')">
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
                {{ $influencers->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
