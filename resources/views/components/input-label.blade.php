@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-white/80']) }}>
    {{ $value ?? $slot }}
</label>
