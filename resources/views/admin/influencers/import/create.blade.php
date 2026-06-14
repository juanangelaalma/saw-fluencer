<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mr-3">{{ __('Import Influencer') }}</h2>
            <a href="{{ route('admin.influencers.import.template') }}" class="btn btn-secondary">{{ __('Download Template') }}</a>
        </div>
    </x-slot>

    <div class="page-stack">
        <div class="page-card">
            <form method="POST" action="{{ route('admin.influencers.import.preview') }}" enctype="multipart/form-data">
                @csrf
                <div>
                    <x-input-label for="file" :value="__('File CSV')" />
                    <input id="file" name="file" type="file" accept=".csv,text/csv" class="mt-1 block w-full" required>
                    <x-input-error class="mt-2" :messages="$errors->get('file')" />
                    <p class="mt-2 text-sm text-gray-600">Header CSV: name, username, lalu nama kriteria sesuai data kriteria.</p>
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <x-primary-button>{{ __('Preview') }}</x-primary-button>
                    <a href="{{ route('admin.influencers.index') }}" class="btn btn-secondary">{{ __('Batal') }}</a>
                </div>
            </form>
        </div>

        @if ($summary)
            <div class="page-card">
                <p>{{ $summary['valid'] }} valid, {{ $summary['invalid'] }} gagal, {{ $summary['skip'] }} dilewati.</p>
            </div>
        @endif

        @if ($rows)
            <div class="card overflow-hidden p-0">
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Baris</th>
                                <th>Nama</th>
                                <th>Username</th>
                                @foreach ($criteria as $criterion)
                                    <th>{{ $criterion->name }}</th>
                                @endforeach
                                <th>Status</th>
                                <th>Pesan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $row)
                                <tr class="{{ $row['status'] === 'invalid' ? 'bg-red-50' : ($row['status'] === 'skip' ? 'bg-yellow-50' : '') }}">
                                    <td>{{ $row['line'] }}</td>
                                    <td>{{ $row['data']['influencer']['name'] }}</td>
                                    <td>{{ $row['data']['influencer']['username'] }}</td>
                                    @foreach ($criteria as $criterion)
                                        <td>{{ $row['data']['criteria'][$criterion->id] ?? '-' }}</td>
                                    @endforeach
                                    <td>{{ $row['status'] }}</td>
                                    <td>{{ $row['message'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.influencers.import.store') }}">
                @csrf
                <input type="hidden" name="rows" value="{{ $encodedRows }}">
                <x-primary-button>{{ __('Proses Import') }}</x-primary-button>
            </form>
        @endif
    </div>
</x-app-layout>
