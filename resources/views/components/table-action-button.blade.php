@props(['variant' => 'default'])

@php
    $classes = match ($variant) {
        'edit' => 'table-action table-action-edit',
        'delete', 'danger' => 'table-action table-action-danger',
        default => 'table-action',
    };
@endphp

<button {{ $attributes->merge(['type' => 'submit', 'class' => $classes]) }}>{{ $slot }}</button>
