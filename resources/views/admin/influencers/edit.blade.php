<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mr-3">{{ __('Edit Influencer') }}</h2>
        </div>
    </x-slot>

    <div class="page-stack">
        @if (session('status'))
            <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
        @endif

        <div class="page-card">
            <form method="POST" action="{{ route('admin.influencers.update', $influencer) }}">
                @method('PUT')
                @include('admin.influencers._form', ['submit' => __('Perbarui')])
            </form>
        </div>

        @if ($influencer->scores->isNotEmpty())
            <div class="card overflow-hidden p-0">
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Kriteria</th>
                                <th>Nilai Mentah</th>
                                <th>Likert</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($influencer->scores->sortBy('criterion.name') as $score)
                                <tr>
                                    <td>{{ $score->criterion->name }}</td>
                                    <td>{{ $score->raw_value }}</td>
                                    <td>{{ $score->likert_value }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
