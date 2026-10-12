@php
    $canManage = auth()->user()->canManageInfluencers();
    $indexRoute = $canManage ? route('admin.influencers.index') : route('kasi.influencers.index');
    $colspan = $criteria->count() + 2 + ($canManage ? 2 : 0);
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mr-3">
                {{ $canManage ? __('Manajemen Influencer') : __('Data Influencer') }}
            </h2>
            @if ($canManage)
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.influencers.import.create') }}" class="btn btn-secondary">{{ __('Import CSV') }}</a>
                    <a href="{{ route('admin.influencers.create') }}" class="btn btn-primary">{{ __('Tambah Influencer') }}</a>
                </div>
            @endif
        </div>
    </x-slot>

    @if ($canManage)
        <form id="selection-form" method="GET" action="{{ route('admin.saw.index') }}">
            @php
                $pageIds = $influencers->getCollection()->modelKeys();
            @endphp
            @foreach (array_diff($selectedIds, $pageIds) as $selectedId)
                <input type="hidden" class="remote-selection" name="selected[]" value="{{ $selectedId }}">
            @endforeach
        </form>
    @endif

    <div>
        @if (session('status'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">{{ session('error') }}</div>
        @endif

        <div class="card overflow-hidden p-0">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-200 px-6 py-4">
                @if ($canManage)
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="text-sm text-gray-600" aria-live="polite">Terpilih: <strong id="selected-count" class="font-semibold text-gray-900">{{ count($selectedIds) }}</strong> data</span>
                        <span class="text-sm text-gray-500">Pilih semua data di halaman ini. Pilihan tersimpan saat berpindah halaman.</span>
                        <button type="button" id="select-all" class="btn btn-secondary">{{ __('Pilih Semua') }}</button>
                        <button type="button" id="clear-selection" class="btn btn-secondary">{{ __('Batal Pilih') }}</button>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <button type="submit" form="selection-form" class="btn btn-primary">{{ __('Hitung Data Terpilih') }}</button>
                        <form method="POST" action="{{ route('admin.influencers.destroy-all') }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus SEMUA data influencer? Tindakan ini tidak dapat dibatalkan.')">
                            @csrf
                            <button type="submit" class="btn btn-danger">{{ __('Hapus Semua') }}</button>
                        </form>
                    </div>
                @else
                    <p class="text-sm text-gray-600">Mode lihat saja. Pengubahan data hanya dapat dilakukan oleh admin.</p>
                @endif
            </div>

            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-200 px-6 py-4">
                <form method="GET" action="{{ $indexRoute }}" class="flex flex-wrap items-center gap-2" role="search">
                    @if ($canManage)
                        @foreach ($selectedIds as $selectedId)
                            <input type="hidden" name="selected[]" value="{{ $selectedId }}">
                        @endforeach
                    @endif
                    <label for="search" class="text-sm text-gray-600">{{ __('Cari') }}</label>
                    <input type="search" id="search" name="search" value="{{ $search }}" placeholder="Nama atau username" class="input w-full sm:w-64" autocomplete="off">
                    <button type="submit" class="btn btn-secondary">{{ __('Cari') }}</button>
                    @if ($search !== '')
                        <a href="{{ $indexRoute }}" class="btn btn-ghost">{{ __('Reset') }}</a>
                    @endif
                </form>
                @if ($search !== '')
                    <p class="text-sm text-gray-600">Hasil untuk "{{ $search }}": {{ $influencers->total() }} data ditemukan.</p>
                @endif
            </div>

            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            @if ($canManage)
                                <th class="w-10">
                                    <label class="inline-flex min-h-11 min-w-11 cursor-pointer items-center justify-center">
                                        <input type="checkbox" id="check-all" form="selection-form" aria-label="Pilih semua data di halaman ini">
                                    </label>
                                </th>
                            @endif
                            <th>Nama</th>
                            <th>Username</th>
                            @foreach ($criteria as $criterion)
                                <th>{{ $criterion->name }}</th>
                            @endforeach
                            @if ($canManage)
                                <th class="num">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($influencers as $influencer)
                            @php
                                $scores = $influencer->scores->keyBy('criterion_id');
                            @endphp
                            <tr>
                                @if ($canManage)
                                    <td>
                                        <label class="inline-flex min-h-11 min-w-11 cursor-pointer items-center justify-center">
                                            <input type="checkbox" class="row-check" form="selection-form" name="selected[]" value="{{ $influencer->id }}" aria-label="Pilih {{ $influencer->name }}" @checked(in_array($influencer->id, $selectedIds, true))>
                                        </label>
                                    </td>
                                @endif
                                <td>{{ $influencer->name }}</td>
                                <td>{{ $influencer->username }}</td>
                                @foreach ($criteria as $criterion)
                                    <td>{{ $scores->get($criterion->id)?->raw_value ?? '-' }}</td>
                                @endforeach
                                @if ($canManage)
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
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $colspan }}" class="text-center text-gray-500">
                                    @if ($search !== '')
                                        Tidak ada influencer yang cocok dengan pencarian "{{ $search }}".
                                    @else
                                        Belum ada data influencer.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-200 px-6 py-4">
                {{ $influencers->links() }}
            </div>
        </div>
    </div>

    @if ($canManage)
        <script>
            (function () {
                var form = document.getElementById('selection-form');
                var checkboxes = Array.prototype.slice.call(document.querySelectorAll('.row-check'));
                var pageIds = new Set(checkboxes.map(function (checkbox) { return checkbox.value; }));
                var selectedIds = new Set(@json(array_map('strval', $selectedIds)));
                var master = document.getElementById('check-all');
                var counter = document.getElementById('selected-count');

                function syncRemoteInputs() {
                    form.querySelectorAll('.remote-selection').forEach(function (input) {
                        input.remove();
                    });

                    selectedIds.forEach(function (id) {
                        if (pageIds.has(id)) {
                            return;
                        }

                        var input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'selected[]';
                        input.value = id;
                        input.className = 'remote-selection';
                        form.appendChild(input);
                    });
                }

                function updateSelection() {
                    var checkedOnPage = 0;

                    checkboxes.forEach(function (checkbox) {
                        checkbox.checked = selectedIds.has(checkbox.value);
                        if (checkbox.checked) {
                            checkedOnPage += 1;
                        }
                    });

                    counter.textContent = selectedIds.size;
                    master.checked = checkboxes.length > 0 && checkedOnPage === checkboxes.length;
                    master.indeterminate = checkedOnPage > 0 && checkedOnPage < checkboxes.length;
                    syncRemoteInputs();
                }

                function setPageSelection(checked) {
                    checkboxes.forEach(function (checkbox) {
                        if (checked) {
                            selectedIds.add(checkbox.value);
                        } else {
                            selectedIds.delete(checkbox.value);
                        }
                    });
                    updateSelection();
                }

                master.addEventListener('change', function () {
                    setPageSelection(master.checked);
                });

                document.getElementById('select-all').addEventListener('click', function () {
                    setPageSelection(true);
                });

                document.getElementById('clear-selection').addEventListener('click', function () {
                    selectedIds.clear();
                    updateSelection();
                });

                checkboxes.forEach(function (checkbox) {
                    checkbox.addEventListener('change', function () {
                        if (checkbox.checked) {
                            selectedIds.add(checkbox.value);
                        } else {
                            selectedIds.delete(checkbox.value);
                        }
                        updateSelection();
                    });
                });

                document.querySelectorAll('a[href*="page="]').forEach(function (link) {
                    link.addEventListener('click', function () {
                        var url = new URL(link.href);

                        Array.from(url.searchParams.keys()).forEach(function (key) {
                            if (key === 'selected' || key.indexOf('selected[') === 0) {
                                url.searchParams.delete(key);
                            }
                        });

                        selectedIds.forEach(function (id) {
                            url.searchParams.append('selected[]', id);
                        });

                        link.href = url.toString();
                    });
                });

                updateSelection();
            })();
        </script>
    @endif
</x-app-layout>
