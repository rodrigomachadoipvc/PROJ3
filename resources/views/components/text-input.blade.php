@props(['disabled' => false])

<input
    @disabled($disabled)
    {{ $attributes->merge([
        'class' => 'rounded-xl border bg-white/5 text-white placeholder-white/40 focus:ring-0 focus:border-white/20',
        'style' => 'border-color: rgba(255,255,255,.10);'
    ]) }}
>
