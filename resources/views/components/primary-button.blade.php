<button
    {{ $attributes->merge([
        'type' => 'submit',
        'class' => 'inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold text-white border transition hover:brightness-110 focus:outline-none',
        'style' => 'border-color: rgba(255,255,255,.12); background: linear-gradient(180deg, rgba(96,165,250,.20), rgba(45,212,191,.12));'
    ]) }}
>
    {{ $slot }}
</button>
