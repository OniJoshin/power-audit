<x-sidebar-layout>
    <x-slot name="header">Power Setups</x-slot>

    <div class="space-y-4">
        @livewire('power-setup-selector', ['selectedSetupId' => session('selected_setup_id' ?? null)])
    </div>
</x-sidebar-layout>