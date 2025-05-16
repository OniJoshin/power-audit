@props(['title', 'value', 'icon' => '', 'color' => 'blue'])

<div class="bg-white shadow rounded p-4">
    <h3 class="text-sm font-semibold text-gray-600 mb-1">
        {{ $title }}
    </h3>
    <p class="text-2xl font-bold text-gray-800 flex items-center gap-2">
        <span class="text-{{ $color }}-500">{{ $icon }}</span>
        {{ $value }}
    </p>
</div>
