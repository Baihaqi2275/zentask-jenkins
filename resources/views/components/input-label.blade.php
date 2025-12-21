@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-medium text-gray-300 mb-2']) }}>
    {{ $value ?? $slot }}
</label>
