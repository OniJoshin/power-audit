@props(['route', 'label', 'icon' => '', 'color' => 'blue'])

<a href="{{ $route }}"
   class="bg-{{ $color }}-600 hover:bg-{{ $color }}-700 text-white px-5 py-3 rounded font-semibold text-sm transition">
    {{ $icon }} {{ $label }}
</a>
