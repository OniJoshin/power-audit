<div class="bg-white shadow rounded p-6 space-y-6">

    @if ($selectedSetupId)
        {{-- Power Configuration Fields --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block font-medium text-gray-700">System Voltage (V)</label>
                <input type="number" wire:model="systemVoltage" class="w-full border rounded px-3 py-2" />
                @error('systemVoltage') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block font-medium text-gray-700">Inverter Efficiency (%)</label>
                <input type="number" wire:model="inverterEfficiency" min="50" max="100" class="w-full border rounded px-3 py-2" />
                @error('inverterEfficiency') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block font-medium text-gray-700">Battery Type</label>
                <select wire:model="batteryType" class="w-full border rounded px-3 py-2">
                    <option value="lead">Lead-Acid (50% usable)</option>
                    <option value="lithium">Lithium (90% usable)</option>
                </select>
                @error('batteryType') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block font-medium text-gray-700">Autonomy Days</label>
                <input type="number" wire:model="autonomyDays" min="1" class="w-full border rounded px-3 py-2" />
                @error('autonomyDays') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Appliance Input Form --}}
        <form wire:submit.prevent="addAppliance" class="space-y-4">
            <h3 class="text-md font-bold text-gray-800 mt-4">Add Appliance</h3>

            <div>
                <label class="block font-medium text-gray-700">Appliance Name</label>
                <input type="text" wire:model="name" class="w-full border rounded px-3 py-2" />
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="block font-medium text-gray-700">Voltage</label>
                    <select wire:model="voltage" class="w-full border rounded px-3 py-2">
                        <option value="12">12V</option>
                        <option value="230">230V</option>
                    </select>
                    @error('voltage') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-medium text-gray-700">Power Draw (W)</label>
                    <input type="number" wire:model="watts" class="w-full border rounded px-3 py-2" />
                    @error('watts') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-medium text-gray-700">Hours/Day</label>
                    <input type="number" wire:model="hours" class="w-full border rounded px-3 py-2" />
                    @error('hours') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-medium text-gray-700">Quantity</label>
                    <input type="number" wire:model="quantity" class="w-full border rounded px-3 py-2" />
                    @error('quantity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-semibold">
                Add Appliance
            </button>
        </form>

        {{-- Appliance List --}}
        <div>
            <h3 class="text-md font-bold text-gray-800">Appliances in Setup</h3>
            <ul class="space-y-2">
                @forelse ($enhancedAppliances as $index => $appliance)
                    <li class="border rounded p-4 bg-gray-50 flex flex-col md:flex-row justify-between items-start md:items-center">
                        <div>
                            <strong>{{ $appliance['name'] }}</strong><br>
                            {{ $appliance['voltage'] }}V • {{ $appliance['watts'] }}W × {{ $appliance['quantity'] }}
                            for {{ $appliance['hours'] }} hrs/day
                        </div>
                        <div class="text-sm mt-2 md:mt-0 text-gray-700">
                            <div><strong>Wh/day:</strong> {{ $appliance['adjustedWh'] }} Wh</div>
                            <div><strong>Ah/day:</strong> {{ $appliance['ah'] }} Ah</div>
                        </div>
                        <button wire:click="removeAppliance({{ $index }})" class="text-red-500 hover:text-red-700 mt-2 md:mt-0 md:ml-4">
                            Remove
                        </button>
                    </li>
                @empty
                    <li class="text-gray-500 italic">No appliances added yet.</li>
                @endforelse
            </ul>
        </div>

        {{-- Summary Section --}}
        <div class="mt-6 bg-gray-100 border rounded p-4 space-y-2">
            <h3 class="text-md font-bold text-gray-800">Daily Usage Summary</h3>
            <p><strong>12V Items:</strong> {{ $dailyTotalWatts12V }} Wh</p>
            <p><strong>230V Items (Adjusted):</strong> {{ $dailyTotalWatts230V }} Wh</p>
            <p><strong>Total Daily Usage:</strong> {{ $totalWhWithInverterLoss }} Wh</p>
            <p><strong>Total Ah (@ {{ $systemVoltage }}V):</strong> {{ $totalAh }} Ah</p>
        </div>

        <div class="mt-4 bg-yellow-100 border border-yellow-300 rounded p-4">
            <h3 class="font-bold text-yellow-800 text-md mb-2">Recommended Battery Bank</h3>
            <p>
                For <strong>{{ $autonomyDays }}</strong> day(s) autonomy using
                <strong>{{ ucfirst($batteryType) }}</strong> batteries:
            </p>
            <p class="text-lg font-semibold text-yellow-900">
                → <strong>{{ $recommendedAh }} Ah</strong> total battery capacity
            </p>
        </div>
    @else
        <div class="text-gray-500 italic">
            Please select or create a Power Setup to begin adding appliances.
        </div>
    @endif
</div>
