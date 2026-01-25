{{-- resources/views/components/dropdown.blade.php --}}
@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1'])

@php
    $alignmentClasses = match ($align) {
        'left' => 'origin-top-left left-0',
        'top' => 'origin-top',
        default => 'origin-top-right right-0',
    };

    $widthClass = match ($width) {
        '48' => 'w-48',
        '56' => 'w-56',
        '64' => 'w-64',
        default => 'w-48',
    };
@endphp

<div class="relative z-[2147483647]" x-data="{ open: false }" @click.away="open = false" @close.stop="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute {{ $alignmentClasses }} mt-2 {{ $widthClass }} rounded-2xl z-[2147483647]"
        style="display: none;"
    >
        <div
            class="rounded-2xl overflow-hidden border shadow-2xl {{ $contentClasses }}"
            style="
                border-color: rgba(255,255,255,.14);
                background: rgba(10,15,24,.92);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                box-shadow: 0 30px 90px rgba(0,0,0,.55);
            "
        >
            {{ $content }}
        </div>
    </div>
</div>
