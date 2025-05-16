<x-sidebar-layout>
    <x-slot name="header">
        Appliance Management
    </x-slot>

    <div class="space-y-4">
        @livewire('appliance-form', ['selectedSetupId' => $selectedSetupId])
    </div>
</x-sidebar-layout>