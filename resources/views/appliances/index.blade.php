<x-sidebar-layout>
    <x-slot name="header">
        Appliance Management
    </x-slot>

    <div class="space-y-4">
        @livewire('appliance-form', ['selectedSetupId' => $selectedSetupId])


        <div class="border-t pt-6">
            @livewire('power-summary-chart', ['selectedSetupId' => $selectedSetupId])
        </div>
    </div>
</x-sidebar-layout>