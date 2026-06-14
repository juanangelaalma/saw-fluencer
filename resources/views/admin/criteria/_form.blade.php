@csrf

<div class="grid gap-6 sm:grid-cols-2" x-data="{ weight: Number('{{ old('weight', $criterion->weight ?? 0) }}'), other: Number('{{ $otherWeightTotal }}') }">
    <div>
        <x-input-label for="code" :value="__('Kode')" />
        <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" :value="old('code', $criterion->code)" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('code')" />
    </div>

    <div>
        <x-input-label for="name" :value="__('Nama Kriteria')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $criterion->name)" required />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label for="weight" :value="__('Bobot (%)')" />
        <x-text-input id="weight" name="weight" type="number" min="0" max="100" class="mt-1 block w-full" x-model.number="weight" required />
        <x-input-error class="mt-2" :messages="$errors->get('weight')" />
        <p class="mt-2 text-sm" :class="(other + weight) <= 100 ? 'text-green-700' : 'text-red-700'">
            Total bobot: <span x-text="other + weight"></span>% / maksimal 100%
        </p>
    </div>

    <div>
        <x-input-label for="type" :value="__('Jenis')" />
        <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            @foreach ($types as $value => $label)
                <option value="{{ $value }}" @selected(old('type', $criterion->type) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('type')" />
    </div>

    <div class="sm:col-span-2">
        <div class="flex items-center gap-3">
            <x-primary-button>{{ $submit }}</x-primary-button>
            <a href="{{ route('admin.criteria.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                {{ __('Batal') }}
            </a>
        </div>
    </div>
</div>
