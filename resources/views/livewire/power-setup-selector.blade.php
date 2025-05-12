<div class="space-y-4">
    <div>
        <label class="block font-semibold text-gray-700 mb-1">Select Power Setup</label>
        <select wire:model="selectedSetupId" class="w-full border rounded px-3 py-2">
            @foreach ($setups as $setup)
                <option value="{{ $setup->id }}">{{ $setup->name }}</option>
            @endforeach
        </select>
        <button wire:click="$dispatch('setupChanged', { id: {{ $selectedSetupId }} })">
            Test Dispatch
        </button>
    </div>

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
</div>
