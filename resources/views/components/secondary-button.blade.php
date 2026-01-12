<button
    {{ $attributes->merge([
        'type' => 'button',
        'class' => 'inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold text-white/85 border transition hover:text-white disabled:opacity-40 disabled:cursor-not-allowed focus:outline-none',
        'style' => 'border-color: rgba(255,255,255,.12); background: rgba(255,255,255,.04);'
    ]) }}
>
    {{ $slot }}
</button>
