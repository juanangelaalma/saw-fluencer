<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Kasi/SPV') }}
        </h2>
    </x-slot>

    <div class="page-stack">
        <div class="card">
            <p class="text-sm text-gray-700">
                {{ __('Akun Kasi/SPV memiliki akses lihat saja untuk mendukung pengambilan keputusan: melihat data influencer, hasil perhitungan SAW, dan perangkingan.') }}
            </p>
        </div>

        <div class="grid-3">
            <a href="{{ route('kasi.influencers.index') }}" class="page-card">
                <p class="eyebrow">1</p>
                <h3 class="mt-2 text-lg font-semibold text-gray-900">Lihat Data Influencer</h3>
                <p class="hint mt-1">Telusuri data influencer beserta nilai tiap kriteria, lengkap dengan pencarian.</p>
            </a>

            <a href="{{ route('kasi.saw.index') }}" class="page-card">
                <p class="eyebrow">2</p>
                <h3 class="mt-2 text-lg font-semibold text-gray-900">Lihat Hasil Perhitungan</h3>
                <p class="hint mt-1">Ikuti seluruh tahap perhitungan SAW: konversi rating, normalisasi, matriks, dan nilai akhir.</p>
            </a>

            <a href="{{ route('saw.history.index') }}" class="page-card">
                <p class="eyebrow">3</p>
                <h3 class="mt-2 text-lg font-semibold text-gray-900">Lihat Riwayat &amp; Perangkingan</h3>
                <p class="hint mt-1">Buka perangkingan perhitungan sebelumnya sebagai bahan pertimbangan keputusan.</p>
            </a>
        </div>
    </div>
</x-app-layout>
