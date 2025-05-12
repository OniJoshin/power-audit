<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PowerSetup;
use Illuminate\Support\Facades\Auth;

class PowerSetupSelector extends Component
{
    public $setups;
    public $selectedSetupId;
    public $newSetupName = '';
    public $systemVoltage = 12;
    public $inverterEfficiency = 85;
    public $batteryType = 'lead';
    public $autonomyDays = 2;

    public function mount()
    {
        $this->loadSetups();
    }

    public function loadSetups()
    {
        $this->setups = Auth::user()->powerSetups()->get();
        $this->selectedSetupId = optional($this->setups->first())->id;
        $this->dispatch('setupChanged', id: $this->selectedSetupId);
    }

    public function createSetup()
    {
        $this->validate([
            'newSetupName' => 'required|string|max:255',
            'systemVoltage' => 'required|numeric|min:1',
            'inverterEfficiency' => 'required|numeric|between:50,100',
            'batteryType' => 'required|in:lead,lithium',
            'autonomyDays' => 'required|integer|min:1',
        ]);

        $setup = PowerSetup::create([
            'user_id' => Auth::id(),
            'name' => $this->newSetupName,
            'system_voltage' => $this->systemVoltage,
            'inverter_efficiency' => $this->inverterEfficiency,
            'battery_type' => $this->batteryType,
            'autonomy_days' => $this->autonomyDays,
        ]);

        $this->newSetupName = '';
        $this->loadSetups();
        $this->selectedSetupId = $setup->id;
        $this->dispatch('setupChanged', id: $setup->id);
    }

    public function updatedSelectedSetupId()
    {
        $this->dispatch('setupChanged', id: $this->selectedSetupId);
    }

    public function render()
    {
        return view('livewire.power-setup-selector');
    }
}
