@php
    $criteriaList = $criteria->map(fn ($criterion) => [
        'code' => $criterion->code,
        'name' => $criterion->name,
        'weight' => $criterion->weight,
        'type' => $criterion->type,
        'type_label' => $criterion->typeLabel(),
        'divisor' => $divisors[$criterion->id] ?? 0,
    ])->values()->all();
    $rowsList = array_map(fn (array $row) => [
        'rank' => $row['rank'],
        'name' => $row['influencer']->name,
        'username' => $row['influencer']->username,
        'score' => $row['score'],
        'criteria_scores' => array_values(array_map(fn (array $score) => [
            'raw' => $score['raw'],
            'likert' => $score['likert'],
            'normalized' => $score['normalized'],
            'weighted' => $score['weighted'],
        ], $row['criteria_scores'])),
    ], $rows);
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Perhitungan SAW') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Konversi rating, normalisasi matriks keputusan, dan perangkingan influencer.') }}</p>
            </div>
            @if (auth()->user()->isAdmin())
                <form method="POST" action="{{ route('saw.history.store') }}" class="flex items-center gap-2">
                    @csrf
                    @foreach ($selectedIds as $id)
                        <input type="hidden" name="selected[]" value="{{ $id }}">
                    @endforeach
                    <button type="submit" class="btn btn-primary">{{ __('Simpan ke Riwayat') }}</button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="page-stack">
        <div class="card">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-sm text-gray-700">
                        @if ($selectedIds !== [])
                            {{ __('Menghitung :selected data terpilih dari :total data influencer.', ['selected' => $totalRows, 'total' => $totalInfluencers]) }}
                        @else
                            {{ __('Menghitung seluruh :total data influencer.', ['total' => $totalRows]) }}
                        @endif
                    </p>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Setiap kolom kriteria menampilkan dua nilai: angka atas adalah nilai likert 1-5 dari sub kriteria, sedangkan angka bawah adalah nilai normalisasi (R) hasil rumus SAW.') }}
                    </p>
                </div>
                <form method="GET" class="flex items-center gap-2">
                    @foreach ($selectedIds ?? [] as $id)
                        <input type="hidden" name="selected[]" value="{{ $id }}">
                    @endforeach
                    <label for="limit" class="text-sm text-gray-600">{{ __('Tampilkan') }}</label>
                    <select id="limit" name="limit" class="rounded-md border-gray-300 text-sm shadow-sm" onchange="this.form.submit()">
                        @foreach ($limitOptions as $option)
                            <option value="{{ $option }}" @selected($limit === $option)>{{ $option === 'all' ? __('Semua') : $option }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            <p class="mt-3 text-sm text-gray-600">{{ __('Menampilkan :shown dari :total data', ['shown' => count($rows), 'total' => $totalRows]) }}</p>
        </div>

        @include('saw._steps', ['criteriaList' => $criteriaList, 'rowsList' => $rowsList])
    </div>
</x-app-layout>
