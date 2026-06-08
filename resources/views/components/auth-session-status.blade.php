@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'rounded-xl border border-[color-mix(in_oklch,var(--ok)_24%,var(--border))] bg-[color-mix(in_oklch,var(--ok)_8%,var(--surface))] p-3 text-sm text-[var(--fg)]']) }} role="status" aria-live="polite">
        {{ $status }}
    </div>
@endif
