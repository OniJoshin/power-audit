
<x-sidebar-layout>
    <x-slot name="header">
        Power Audit Summary
    </x-slot>

    <div class="space-y-4">
        @livewire('power-summary-chart', ['selectedSetupId' => $selectedSetupId])
    </div>
</x-sidebar-layout>
