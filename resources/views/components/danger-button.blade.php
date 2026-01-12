<button
    {{ $attributes->merge([
        'type' => 'submit',
        'class' => 'inline-flex items-center justify-center px-4 py-2 rounded-xl text-sm font-semibold text-white border transition hover:brightness-110 focus:outline-none',
        'style' => 'border-color: rgba(248,113,113,.35); background: rgba(248,113,113,.18);'
    ]) }}
>
    {{ $slot }}
</button>
