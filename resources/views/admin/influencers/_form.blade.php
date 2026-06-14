@csrf

<div class="grid gap-6 sm:grid-cols-2">
    <div>
        <x-input-label for="name" :value="__('Nama Influencer')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $influencer->name)" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label for="username" :value="__('Username')" />
        <x-text-input id="username" name="username" type="text" class="mt-1 block w-full" :value="old('username', $influencer->username)" required />
        <x-input-error class="mt-2" :messages="$errors->get('username')" />
    </div>

    @foreach ($criteria as $criterion)
        @php
            $score = $scores->get($criterion->id);
            $value = old('criteria.'.$criterion->id, $score?->raw_value);
        @endphp

        <div>
            <x-input-label for="criteria_{{ $criterion->id }}" :value="$criterion->name" />
            <x-formatted-number-input id="criteria_{{ $criterion->id }}" name="criteria[{{ $criterion->id }}]" :value="$value" required />
            <x-input-error class="mt-2" :messages="$errors->get('criteria.'.$criterion->id)" />
        </div>
    @endforeach
</div>

<div class="mt-6 flex items-center gap-3">
    <x-primary-button>{{ $submit }}</x-primary-button>
    <a href="{{ route('admin.influencers.index') }}" class="btn btn-secondary">{{ __('Batal') }}</a>
</div>
