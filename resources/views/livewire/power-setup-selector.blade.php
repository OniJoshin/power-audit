<div class="space-y-4">
    <div>
        <button wire:click="$toggle('showCreateForm')"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium">
            {{ $showCreateForm ? 'Cancel' : 'Create New Power Setup' }}
        </button>
        

        <label class="block font-semibold text-gray-700 mb-1 mt-4">Select Power Setup</label>
        <select
            class="w-full border rounded px-3 py-2"
            wire:change="onSelectSetup($event.target.value)"
        >
            <option value="">-- Please select a setup --</option>
            @foreach ($setups as $setup)
                <option value="{{ $setup->id }}">{{ $setup->name }}</option>
            @endforeach
        </select>


       @if ($currentSetup)
            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('pdf.export', $currentSetup->id) }}"
                    target="_blank"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Download PDF Report
                </a>
                <div class="flex gap-4">
                    <button wire:click="startEditingSetup"
                            class="text-sm text-blue-600 hover:underline">
                        Edit "{{ $currentSetup->name }}"
                    </button>
                    <button wire:click="deleteSetup"
                            onclick="return confirm('Are you sure you want to delete this setup?')"
                            class="text-sm text-red-600 hover:underline">
                        Delete Setup
                    </button>
                </div>
            </div>
            
        @endif




        @if ($editingSetup)
            <div class="mt-4 space-y-3 bg-gray-50 border border-gray-300 rounded p-4">
                <h4 class="text-sm font-semibold text-gray-700">Edit Setup</h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Setup Name</label>
                        <input type="text" wire:model="editSetupName" class="w-full border rounded px-2 py-1" />
                        @error('editSetupName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">System Voltage</label>
                        <input type="number" wire:model="editSystemVoltage" class="w-full border rounded px-2 py-1" />
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Inverter Efficiency (%)</label>
                        <input type="number" wire:model="editInverterEfficiency" class="w-full border rounded px-2 py-1" />
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Battery Type</label>
                        <select wire:model="editBatteryType" class="w-full border rounded px-2 py-1">
                            <option value="lead">Lead-Acid</option>
                            <option value="lithium">Lithium</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Autonomy Days</label>
                        <input type="number" wire:model="editAutonomyDays" class="w-full border rounded px-2 py-1" />
                    </div>
                </div>

                <div class="flex gap-2 mt-3">
                    <button wire:click="updateSetup"
                            class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded">
                        Save Changes
                    </button>
                    <button wire:click="$set('editingSetup', false)"
                            class="text-gray-600 hover:underline">
                        Cancel
                    </button>
                </div>
            </div>
        @endif


    </div>

    @if ($showCreateForm)
        <div class="border-t pt-4">
            <h3 class="text-md font-bold text-gray-700 mb-3">Create New Power Setup</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Setup Name</label>
                    <input type="text" wire:model="newSetupName" placeholder="e.g. Summer Setup"
                        class="w-full border rounded px-3 py-2" />
                    @error('newSetupName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">System Voltage (V)</label>
                    <input type="number" wire:model="systemVoltage" class="w-full border rounded px-3 py-2" />
                    @error('systemVoltage') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Inverter Efficiency (%)</label>
                    <input type="number" wire:model="inverterEfficiency" class="w-full border rounded px-3 py-2" />
                    @error('inverterEfficiency') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Battery Type</label>
                    <select wire:model="batteryType" class="w-full border rounded px-3 py-2">
                        <option value="lead">Lead-Acid</option>
                        <option value="lithium">Lithium</option>
                    </select>
                    @error('batteryType') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Autonomy Days</label>
                    <input type="number" wire:model="autonomyDays" class="w-full border rounded px-3 py-2" />
                    @error('autonomyDays') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <button wire:click="createSetup"
                    class="mt-4 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-semibold">
                + Create Setup
            </button>
        </div>
    @endif
</div>
