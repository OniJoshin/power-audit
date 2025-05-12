<div class="bg-white shadow rounded p-6 space-y-6">

    {{-- Inverter Efficiency --}}
    <div class="mb-4">
        <label class="block font-medium text-gray-700">Inverter Efficiency (%)</label>
        <input type="number" wire:model="inverterEfficiency" min="50" max="100" class="w-full border rounded px-3 py-2" />
        <div x-data="{ info: false }" class="mt-1 text-sm text-gray-600">
            <span @click="info = !info" class="cursor-pointer underline">What does this mean?</span>
            <div x-show="info" class="mt-2 bg-white border p-3 rounded shadow text-left">
                Inverter efficiency represents how much power is lost converting 12V battery power to 230V AC.
                For example, with 85% efficiency, a 100W appliance actually pulls ~117.6W from the battery.
            </div>
        </div>
    </div>

    {{-- System Voltage Input --}}
    <div class="mb-4">
        <label class="block font-medium text-gray-700">System Voltage (V)</label>
        <input type="number" wire:model="systemVoltage" min="1" class="w-full border rounded px-3 py-2" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block font-medium text-gray-700">Battery Type</label>
            <select wire:model="batteryType" class="w-full border rounded px-3 py-2">
                <option value="lead">Lead-Acid (50% usable)</option>
                <option value="lithium">Lithium (90% usable)</option>
            </select>
        </div>
        <div>
            <label class="block font-medium text-gray-700">Autonomy Days</label>
            <input type="number" wire:model="autonomyDays" min="1" class="w-full border rounded px-3 py-2" />
        </div>
    </div>


    {{-- Appliance Input Form --}}
    <form wire:submit.prevent="addAppliance" class="space-y-4">
        <div>
            <label class="block font-medium text-gray-700">Appliance Name</label>
            <input type="text" wire:model="name" wire:key="name-{{ $formResetCounter }}" class="w-full border rounded px-3 py-2" />
            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label class="block font-medium text-gray-700">Voltage</label>
                <select wire:model="voltage" wire:key="voltage-{{ $formResetCounter }}" class="w-full border rounded px-3 py-2">
                    <option value="12">12V</option>
                    <option value="230">230V</option>
                </select>
                @error('voltage') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block font-medium text-gray-700">Power Draw (W)</label>
                <input type="number" wire:model="watts" wire:key="watts-{{ $formResetCounter }}" class="w-full border rounded px-3 py-2" />
                @error('watts') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block font-medium text-gray-700">Hours/Day</label>
                <input type="number" wire:model="hours" wire:key="hours-{{ $formResetCounter }}" class="w-full border rounded px-3 py-2" />
                @error('hours') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block font-medium text-gray-700">Quantity</label>
                <input type="number" wire:model="quantity" wire:key="quantity-{{ $formResetCounter }}" class="w-full border rounded px-3 py-2" />
                @error('quantity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-semibold flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Appliance
        </button>
    </form>


    {{-- Appliance List --}}
    <div>
        <h2 class="text-lg font-bold mb-3 text-gray-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18"></path>
            </svg>
            Added Appliances
        </h2>
        <ul class="space-y-2">
            @forelse ($displayAppliances as $index => $appliance)
                <li class="border rounded p-4 bg-gray-50">
                    <div class="flex justify-between items-start flex-col md:flex-row md:items-center md:space-x-6">
                        <div>
                            <strong>{{ $appliance['name'] }}</strong><br>
                            {{ $appliance['voltage'] }}V — {{ $appliance['watts'] }}W × {{ $appliance['quantity'] }} for {{ $appliance['hours'] }} hrs/day
                        </div>
                        <div class="mt-2 md:mt-0 text-sm text-gray-700 space-y-1">
                            <div><strong>Wh/day:</strong> {{ $appliance['adjustedWh'] }} Wh</div>
                            <div><strong>Ah/day:</strong> {{ $appliance['ah'] }} Ah</div>
                        </div>
                        <button wire:click="removeAppliance({{ $index }})" class="text-red-500 hover:text-red-700 ml-auto md:ml-0 md:mt-0 mt-3">
                            <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Remove
                        </button>
                    </div>
                </li>
            @empty
                <li class="text-gray-500 italic">No appliances added yet.</li>
            @endforelse
        </ul>
    </div>

    {{-- Summary Section --}}
    <div class="mt-6 bg-gray-100 border rounded p-4 space-y-2">
        <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            Daily Usage Summary
        </h3>
        <p><strong>12V Items:</strong> {{ $dailyTotalWatts12V }} Wh</p>
        <p><strong>230V Items (Adjusted for Inverter):</strong> {{ $dailyTotalWatts230V }} Wh</p>
        <p><strong>Total Daily Usage:</strong> {{ $totalWhWithInverterLoss }} Wh</p>
        <p><strong>Total Daily Amps (Ah @ {{ $systemVoltage }}V):</strong> {{ $totalAh }} Ah</p>
    </div>

    {{-- Battery Suggestion --}}
    <div class="mt-6 bg-yellow-100 border border-yellow-400 rounded p-4 space-y-2">
        <h3 class="font-bold text-lg text-yellow-800 flex items-center gap-2">
            <svg class="w-6 h-6 text-yellow-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V7a2 2 0 00-2-2h-3V3a1 1 0 10-2 0v2H9V3a1 1 0 10-2 0v2H4a2 2 0 00-2 2v6m18 0a2 2 0 01-2 2H4a2 2 0 01-2-2m18 0v6a2 2 0 01-2 2H4a2 2 0 01-2-2v-6" />
            </svg>
            Recommended Battery Bank
        </h3>
        <p>
            For <strong>{{ $autonomyDays }} day(s)</strong> of autonomy with a
            <strong>{{ ucfirst($batteryType) }}</strong> battery system at <strong>{{ $systemVoltage }}V</strong>:
        </p>
        <p class="text-xl font-semibold text-yellow-900">
            👉 <strong>{{ $recommendedAh }} Ah</strong> total battery capacity needed.
        </p>
    </div>
</div>
