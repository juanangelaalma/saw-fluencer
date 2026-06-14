<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mr-3">{{ __('Sub Kriteria') }}</h2>
            <p class="mt-1 text-sm text-gray-600">{{ $criterion->code }} - {{ $criterion->name }}</p>
        </div>
    </x-slot>

    <div class="page-stack">
        <div>
            @if (session('status'))
                <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
            @endif

            <div class="page-card">
                <form method="POST" action="{{ route('admin.criteria.sub-criteria.update', $criterion) }}">
                    @csrf
                    @method('PUT')

                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Level</th>
                                    <th>Label</th>
                                    <th>Batas Bawah</th>
                                    <th>Batas Atas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($subCriteria as $index => $subCriterion)
                                    <tr>
                                        <td>
                                            {{ $subCriterion->level }}
                                            <input type="hidden" name="sub_criteria[{{ $index }}][level]" value="{{ $subCriterion->level }}">
                                        </td>
                                        <td>
                                            <x-text-input name="sub_criteria[{{ $index }}][label]" type="text" class="block w-full" :value="old('sub_criteria.'.$index.'.label', $subCriterion->label)" required />
                                            <x-input-error class="mt-2" :messages="$errors->get('sub_criteria.'.$index.'.label')" />
                                        </td>
                                        <td>
                                            <x-text-input name="sub_criteria[{{ $index }}][min_value]" type="number" step="0.01" class="block w-full" :value="old('sub_criteria.'.$index.'.min_value', $subCriterion->min_value)" />
                                            <x-input-error class="mt-2" :messages="$errors->get('sub_criteria.'.$index.'.min_value')" />
                                        </td>
                                        <td>
                                            <x-text-input name="sub_criteria[{{ $index }}][max_value]" type="number" step="0.01" class="block w-full" :value="old('sub_criteria.'.$index.'.max_value', $subCriterion->max_value)" />
                                            <x-input-error class="mt-2" :messages="$errors->get('sub_criteria.'.$index.'.max_value')" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <x-input-error class="mt-4" :messages="$errors->get('sub_criteria')" />

                    <div class="mt-6 flex items-center gap-3">
                        <x-primary-button>{{ __('Perbarui') }}</x-primary-button>
                        <a href="{{ route('admin.criteria.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            {{ __('Batal') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
