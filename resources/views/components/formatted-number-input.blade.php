@props(['name', 'id' => null, 'value' => null, 'required' => false])

@php
    $inputId = $id ?? $name;
    $rawValue = $value;
    $displayValue = $rawValue;

    if ($rawValue !== null && $rawValue !== '') {
        $parts = explode('.', (string) $rawValue, 2);
        $displayValue = number_format((int) $parts[0], 0, ',', '.').(isset($parts[1]) && (int) $parts[1] > 0 ? ','.rtrim($parts[1], '0') : '');
    }
@endphp

<div x-data="{
    raw: @js((string) $rawValue),
    format(value) {
        const normalized = String(value ?? '').replace(/[^0-9,.]/g, '').replace(',', '.');
        const parts = normalized.split('.');
        const integer = (parts.shift() ?? '').replace(/\D/g, '');
        const decimal = parts.join('').replace(/\D/g, '');
        const formatted = integer.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

        return decimal.length > 0 ? `${formatted},${decimal}` : formatted;
    },
    clean(value) {
        const normalized = String(value ?? '').replace(/[^0-9,.]/g, '').replace(',', '.');
        const parts = normalized.split('.');
        const integer = (parts.shift() ?? '').replace(/\D/g, '');
        const decimal = parts.join('').replace(/\D/g, '');

        return decimal.length > 0 ? `${integer}.${decimal}` : integer;
    }
}">
    <input type="hidden" name="{{ $name }}" x-model="raw" @required($required)>
    <input id="{{ $inputId }}" type="text" inputmode="decimal" class="input mt-1 block w-full" value="{{ $displayValue }}" x-on:input="raw = clean($event.target.value); $event.target.value = format($event.target.value)" {{ $attributes->except(['name', 'id', 'value', 'required']) }} @required($required)>
</div>
