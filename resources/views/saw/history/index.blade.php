<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Riwayat Perhitungan') }}</h2>
            <p class="mt-1 text-sm text-gray-600">{{ __('Hasil perhitungan dan perangkingan yang telah disimpan sebelumnya.') }}</p>
        </div>
    </x-slot>

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
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Dihitung Oleh</th>
                            <th class="num">Jumlah Data</th>
                            <th>Peringkat Teratas</th>
                            <th class="num">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($calculations as $index => $calculation)
                            @php($topRow = $calculation->topRow())
                            <tr>
                                <td>{{ $calculations->firstItem() + $index }}</td>
                                <td>{{ $calculation->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div>{{ $calculation->user?->username ?? 'Pengguna tidak ditemukan' }}</div>
                                    <div class="text-xs text-gray-500">{{ $calculation->user?->roleLabel() ?? '-' }}</div>
                                </td>
                                <td class="num">{{ $calculation->total_influencers }}</td>
                                <td>
                                    @if ($topRow)
                                        <div>#1 {{ $topRow['name'] }} ({{ $topRow['username'] }})</div>
                                        <div class="text-xs text-gray-500">Nilai akhir: {{ number_format((float) $topRow['score'], 2, ',', '.') }}</div>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <x-table-action-link :href="route('saw.history.show', $calculation)" variant="edit">Lihat</x-table-action-link>
                                        @if (auth()->user()->isAdmin())
                                            <form method="POST" action="{{ route('saw.history.destroy', $calculation) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus riwayat perhitungan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <x-table-action-button variant="delete">Hapus</x-table-action-button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-gray-500">Belum ada riwayat perhitungan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-200 px-6 py-4">
                {{ $calculations->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
