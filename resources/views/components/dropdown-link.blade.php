@props(['href'])

<a
    href="{{ $href }}"
    {{ $attributes->merge([
        'class' => 'block w-full px-4 py-2 text-left text-sm leading-5
                    text-white/80 hover:text-white hover:bg-white/5
                    focus:outline-none focus:bg-white/5 focus:text-white
                    transition'
    ]) }}
>
    {{ $slot }}
</a>
