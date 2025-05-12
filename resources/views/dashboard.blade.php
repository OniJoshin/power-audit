<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-bold mb-4">Power Setup and Appliance Management</h1>
                    <p class="mb-4">Manage your power setups and appliances efficiently.</p>

                    <h2 class="text-lg font-bold mb-1">Power Setup</h2>
                    @livewire('power-setup-selector')
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-lg font-bold mb-4">Appliance Management</h2>
                    @livewire('appliance-form')
                </div>
            </div>
        </div>
    </div>
    

</x-app-layout>
