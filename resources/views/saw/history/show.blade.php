<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Detail Riwayat Perhitungan') }}</h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Dihitung pada :date oleh :user dengan :total data influencer.', [
                        'date' => $calculation->created_at->format('d/m/Y H:i'),
                        'user' => $calculation->user?->username ?? 'Pengguna tidak ditemukan',
                        'total' => $calculation->total_influencers,
                    ]) }}
                </p>
            </div>
            <a href="{{ route('saw.history.index') }}" class="btn btn-secondary">{{ __('Kembali ke Riwayat') }}</a>
        </div>
    </x-slot>

    <div class="page-stack">
        @include('saw._steps', ['criteriaList' => $criteriaList, 'rowsList' => $rowsList])
    </div>
</x-app-layout>
