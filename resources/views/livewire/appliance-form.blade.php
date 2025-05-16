<div class="bg-white shadow rounded p-6 space-y-6">
    @if ($selectedSetupId)
        {{-- Setup Overview --}}
        <div class="grid grid-cols-1 md:grid-cols-4 text-gray-700 gap-4">
            <p><strong>System Voltage:</strong> {{ $setup->system_voltage }}V</p>
            <p><strong>Inverter Efficiency:</strong> {{ $setup->inverter_efficiency }}%</p>
            <p><strong>Battery Type:</strong> {{ ucfirst($setup->battery_type) }}</p>
            <p><strong>Autonomy Days:</strong> {{ $setup->autonomy_days }}</p>
        </div>

        {{-- Appliance Input Form --}}
        <form wire:submit.prevent="{{ $editingApplianceId ? 'updateAppliance' : 'addAppliance' }}" class="space-y-4">
            <h3 class="text-md font-bold text-gray-800 mt-4">
                {{ $editingApplianceId ? 'Edit Appliance' : 'Add Appliance' }}
            </h3>

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

            <div class="flex items-center gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-semibold">
                    {{ $editingApplianceId ? 'Save Changes' : 'Add Appliance' }}
                </button>
                @if ($editingApplianceId)
                    <button wire:click.prevent="resetForm" class="text-gray-600 hover:underline">Cancel</button>
                @endif
            </div>
        </form>

        {{-- Appliance List --}}
        <div class="mt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M20.25 7.5v9a2.25 2.25 0 01-1.2 2L12 21.75l-7.05-3.25a2.25 2.25 0 01-1.2-2v-9a2.25 2.25 0 011.2-2L12 2.25l7.05 3.25a2.25 2.25 0 011.2 2z" />
                </svg>
                Appliances in "{{ $setup->name }}"
            </h3>

            <ul class="space-y-4">
                @forelse ($enhancedAppliances as $index => $appliance)
                    <li class="bg-white shadow-sm border rounded-lg p-4 flex flex-col md:flex-row justify-between md:items-center">
                        <div class="mb-2 md:mb-0">
                            <div class="text-base font-semibold text-gray-800 flex items-center gap-1">
                                <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" stroke-width="1.5"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13.5 4.5L6 13.5h5.25l-1.5 6 7.5-9H13.5l1.5-6z" />
                                </svg>
                                {{ $appliance['name'] }}
                            </div>
                            <div class="text-sm text-gray-600">
                                {{ $appliance['voltage'] }}V • {{ $appliance['watts'] }}W × {{ $appliance['quantity'] }} for {{ $appliance['hours'] }} hrs/day
                            </div>
                        </div>

                        <div class="flex flex-col md:flex-row items-start md:items-center gap-4">
                            <div class="text-sm text-gray-700">
                                <div><strong>Wh/day:</strong> {{ $appliance['adjustedWh'] }} Wh</div>
                                <div><strong>Ah/day:</strong> {{ $appliance['ah'] }} Ah</div>
                            </div>

                            <div class="flex gap-2">
                                <button wire:click="editAppliance({{ $appliance['id'] }})"
                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.862 3.487a2.475 2.475 0 113.5 3.5L7.5 19.849l-4.5 1 1-4.5 12.362-12.362z" />
                                    </svg>
                                    Edit
                                </button>
                                <button wire:click="removeAppliance({{ $index }})"
                                    class="text-red-600 hover:text-red-800 text-sm font-medium flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6 7.5h12M9.75 10.5v6M14.25 10.5v6M4.5 7.5H19.5l-.375 12.75a2.25 2.25 0 01-2.25 2.25H7.125a2.25 2.25 0 01-2.25-2.25L4.5 7.5zM9.75 3h4.5v1.5h-4.5V3z" />
                                    </svg>
                                    Remove
                                </button>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="text-gray-500 italic">No appliances added yet.</li>
                @endforelse
            </ul>
        </div>
    @else
        <p class="text-gray-500 italic">Please select or create a Power Setup to begin adding appliances.</p>
    @endif
</div>
