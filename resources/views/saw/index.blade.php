@php
    $formatSaw = fn (float $value, int $precision = 4) => rtrim(rtrim(number_format($value, $precision, ',', '.'), '0'), ',');
@endphp

<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Perhitungan SAW') }}</h2>
            <p class="mt-1 text-sm text-gray-600">{{ __('Normalisasi matriks keputusan dan perangkingan influencer.') }}</p>
        </div>
    </x-slot>

    <div class="page-stack">
        <div class="card">
            <p class="text-sm text-gray-700">
                {{ __('Setiap kolom kriteria menampilkan dua nilai: angka atas adalah nilai likert 1-5 dari sub kriteria, sedangkan angka bawah adalah nilai normalisasi (R) hasil rumus SAW.') }}
            </p>
        </div>

        <div class="card overflow-hidden p-0">
            <div class="flex items-center justify-between gap-4 border-b border-gray-200 px-6 py-4">
                <p class="text-sm text-gray-600">{{ __('Menampilkan :shown dari :total data', ['shown' => count($rows), 'total' => $totalRows]) }}</p>
                <form method="GET" class="flex items-center gap-2">
                    <label for="limit" class="text-sm text-gray-600">{{ __('Tampilkan') }}</label>
                    <select id="limit" name="limit" class="rounded-md border-gray-300 text-sm shadow-sm" onchange="this.form.submit()">
                        @foreach ($limitOptions as $option)
                            <option value="{{ $option }}" @selected($limit === $option)>{{ $option === 'all' ? __('Semua') : $option }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Ranking</th>
                            <th>Nama</th>
                            <th>Username</th>
                            @foreach ($criteria as $criterion)
                                <th>{{ $criterion->code }}<br><span class="text-xs font-normal text-gray-500">{{ $criterion->name }}</span></th>
                            @endforeach
                            <th class="num">Nilai Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            @php
                                $rankStyles = [
                                    1 => ['row' => 'bg-yellow-50', 'badge' => 'bg-yellow-100 text-yellow-800 ring-yellow-300'],
                                    2 => ['row' => 'bg-slate-50', 'badge' => 'bg-slate-100 text-slate-800 ring-slate-300'],
                                    3 => ['row' => 'bg-orange-50', 'badge' => 'bg-orange-100 text-orange-800 ring-orange-300'],
                                ];
                                $rankStyle = $rankStyles[$row['rank']] ?? null;
                            @endphp
                            <tr @class([$rankStyle['row'] ?? null])>
                                <td>
                                    @if ($rankStyle)
                                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full text-sm font-semibold ring-1 ring-inset {{ $rankStyle['badge'] }}">{{ $row['rank'] }}</span>
                                    @else
                                        {{ $row['rank'] }}
                                    @endif
                                </td>
                                <td>{{ $row['influencer']->name }}</td>
                                <td>{{ $row['influencer']->username }}</td>
                                @foreach ($criteria as $criterion)
                                    @php($score = $row['criteria_scores'][$criterion->id])
                                    <td>
                                        <div><span class="text-xs text-gray-500">Likert:</span> {{ $score['likert'] }}</div>
                                        <div class="text-xs text-gray-500">R: {{ $formatSaw($score['normalized']) }}</div>
                                    </td>
                                @endforeach
                                <td class="num font-semibold">{{ number_format($row['score'], 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 4 + $criteria->count() }}" class="text-center text-gray-500">{{ __('Belum ada data influencer.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <h3 class="mb-4 text-lg font-semibold text-gray-900">{{ __('Pembagi Normalisasi') }}</h3>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Kriteria</th>
                            <th>Tipe</th>
                            <th>Bobot</th>
                            <th>Pembagi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($criteria as $criterion)
                            <tr>
                                <td>{{ $criterion->code }}</td>
                                <td>{{ $criterion->name }}</td>
                                <td>{{ $criterion->typeLabel() }}</td>
                                <td>{{ $criterion->weight }}%</td>
                                <td>{{ $divisors[$criterion->id] ?? 0 }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
