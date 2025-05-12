<div class="space-y-4">
    <div>
        <label class="block font-semibold text-gray-700 mb-1">Select Power Setup</label>
        <select wire:model="selectedSetupId" class="w-full border rounded px-3 py-2">
            @foreach ($setups as $setup)
                <option value="{{ $setup->id }}">{{ $setup->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="border-t pt-4">
        <h3 class="text-sm font-bold text-gray-600 mb-2">Create New Setup</h3>

        <div class="space-y-2">
            <input type="text" wire:model="newSetupName" placeholder="Setup Name" class="w-full border rounded px-3 py-2" />
            <input type="number" wire:model="systemVoltage" placeholder="System Voltage" class="w-full border rounded px-3 py-2" />
            <input type="number" wire:model="inverterEfficiency" placeholder="Inverter Efficiency" class="w-full border rounded px-3 py-2" />
            <select wire:model="batteryType" class="w-full border rounded px-3 py-2">
                <option value="lead">Lead-Acid</option>
                <option value="lithium">Lithium</option>
            </select>
            <input type="number" wire:model="autonomyDays" placeholder="Autonomy Days" class="w-full border rounded px-3 py-2" />
        </div>

        <button wire:click="createSetup" class="mt-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
            + Create Setup
        </button>
    </div>
</div>
