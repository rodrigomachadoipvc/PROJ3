@props(['status'])

@if ($status)
    <div
        {{ $attributes->merge([
            'class' => 'mb-4 rounded-xl border px-4 py-3 text-sm',
            'style' => 'border-color: rgba(34,197,94,.25); background: rgba(34,197,94,.10); color: rgba(220,252,231,.95);'
        ]) }}
    >
        {{ $status }}
    </div>
@endif
