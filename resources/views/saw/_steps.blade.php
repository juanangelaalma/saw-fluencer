@php
    $fmt = fn ($value, int $precision = 4) => rtrim(rtrim(number_format((float) $value, $precision, ',', '.'), '0'), ',');
    $raw = fn ($value) => $value === null ? '-' : number_format((float) $value, 2, ',', '.');
    $rankStyles = [
        1 => ['row' => 'bg-yellow-50', 'badge' => 'bg-yellow-100 text-yellow-800 ring-yellow-300'],
        2 => ['row' => 'bg-slate-50', 'badge' => 'bg-slate-100 text-slate-800 ring-slate-300'],
        3 => ['row' => 'bg-orange-50', 'badge' => 'bg-orange-100 text-orange-800 ring-orange-300'],
    ];
@endphp

<div class="page-stack">
    <div class="card">
        <h3 class="mb-2 text-lg font-semibold text-gray-900">1. Konversi Nilai Input ke Nilai Rating (Likert)</h3>
        <p class="hint mb-4">Nilai mentah dari tiap kriteria dikonversi menjadi nilai likert 1-5 berdasarkan rentang sub kriteria.</p>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Username</th>
                        @foreach ($criteriaList as $criterion)
                            <th>{{ $criterion['code'] }}<br><span class="text-xs font-normal text-gray-500">{{ $criterion['name'] }}</span></th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rowsList as $row)
                        <tr>
                            <td>{{ $row['name'] }}</td>
                            <td>{{ $row['username'] }}</td>
                            @foreach ($criteriaList as $index => $criterion)
                                @php($score = $row['criteria_scores'][$index] ?? null)
                                <td>
                                    <div>{{ $raw($score['raw'] ?? null) }} <span class="text-gray-400">&rarr;</span> <span class="font-semibold">{{ $score['likert'] ?? '-' }}</span></div>
                                    <div class="text-xs text-gray-500">input &rarr; rating</div>
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 2 + count($criteriaList) }}" class="text-center text-gray-500">Belum ada data influencer.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <h3 class="mb-2 text-lg font-semibold text-gray-900">2. Perhitungan Normalisasi (Pembagi)</h3>
        <p class="hint mb-4">Kriteria benefit menggunakan nilai maksimum sebagai pembagi, kriteria cost menggunakan nilai minimum.</p>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Kriteria</th>
                        <th>Tipe</th>
                        <th>Bobot</th>
                        <th>Pembagi</th>
                        <th>Rumus Normalisasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($criteriaList as $criterion)
                        <tr>
                            <td>{{ $criterion['code'] }}</td>
                            <td>{{ $criterion['name'] }}</td>
                            <td>{{ $criterion['type_label'] }}</td>
                            <td>{{ $criterion['weight'] }}%</td>
                            <td class="num">{{ $criterion['divisor'] }}</td>
                            <td class="text-xs text-gray-500">
                                {{ $criterion['type'] === 'cost' ? 'R = pembagi / nilai' : 'R = nilai / pembagi' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <h3 class="mb-2 text-lg font-semibold text-gray-900">3. Hasil Matriks Normalisasi (R)</h3>
        <p class="hint mb-4">Hasil pembagian nilai rating setiap influencer dengan pembagi normalisasi tiap kriteria.</p>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Username</th>
                        @foreach ($criteriaList as $criterion)
                            <th>{{ $criterion['code'] }}<br><span class="text-xs font-normal text-gray-500">{{ $criterion['name'] }}</span></th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rowsList as $row)
                        <tr>
                            <td>{{ $row['name'] }}</td>
                            <td>{{ $row['username'] }}</td>
                            @foreach ($criteriaList as $index => $criterion)
                                @php($score = $row['criteria_scores'][$index] ?? null)
                                <td class="num">{{ $fmt($score['normalized'] ?? 0) }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 2 + count($criteriaList) }}" class="text-center text-gray-500">Belum ada data influencer.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <h3 class="mb-2 text-lg font-semibold text-gray-900">4. Nilai Akhir (V)</h3>
        <p class="hint mb-4">Nilai normalisasi dikalikan dengan bobot kriteria, kemudian dijumlahkan untuk memperoleh nilai akhir.</p>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Username</th>
                        @foreach ($criteriaList as $criterion)
                            <th>{{ $criterion['code'] }}<br><span class="text-xs font-normal text-gray-500">{{ $criterion['weight'] }}%</span></th>
                        @endforeach
                        <th class="num">Nilai Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rowsList as $row)
                        <tr>
                            <td>{{ $row['name'] }}</td>
                            <td>{{ $row['username'] }}</td>
                            @foreach ($criteriaList as $index => $criterion)
                                @php($score = $row['criteria_scores'][$index] ?? null)
                                <td class="num">{{ $fmt($score['weighted'] ?? 0) }}</td>
                            @endforeach
                            <td class="num font-semibold">{{ $fmt($row['score'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 3 + count($criteriaList) }}" class="text-center text-gray-500">Belum ada data influencer.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <h3 class="mb-2 text-lg font-semibold text-gray-900">5. Perangkingan</h3>
        <p class="hint mb-4">Urutan influencer berdasarkan nilai akhir dari yang tertinggi.</p>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Ranking</th>
                        <th>Nama</th>
                        <th>Username</th>
                        @foreach ($criteriaList as $criterion)
                            <th>{{ $criterion['code'] }}<br><span class="text-xs font-normal text-gray-500">Likert / R</span></th>
                        @endforeach
                        <th class="num">Nilai Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rowsList as $row)
                        @php($rankStyle = $rankStyles[$row['rank']] ?? null)
                        <tr @class([$rankStyle['row'] ?? null])>
                            <td>
                                @if ($rankStyle)
                                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full text-sm font-semibold ring-1 ring-inset {{ $rankStyle['badge'] }}">{{ $row['rank'] }}</span>
                                @else
                                    {{ $row['rank'] }}
                                @endif
                            </td>
                            <td>{{ $row['name'] }}</td>
                            <td>{{ $row['username'] }}</td>
                            @foreach ($criteriaList as $index => $criterion)
                                @php($score = $row['criteria_scores'][$index] ?? null)
                                <td>
                                    <div><span class="text-xs text-gray-500">Likert:</span> {{ $score['likert'] ?? '-' }}</div>
                                    <div class="text-xs text-gray-500">R: {{ $fmt($score['normalized'] ?? 0) }}</div>
                                </td>
                            @endforeach
                            <td class="num font-semibold">{{ $fmt($row['score'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 4 + count($criteriaList) }}" class="text-center text-gray-500">Belum ada data influencer.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
