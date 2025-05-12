<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PowerSetup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PowerSetupSelector extends Component
{
    public $setups;
    public $selectedSetupId;
    public $newSetupName = '';
    public $systemVoltage = 12;
    public $inverterEfficiency = 85;
    public $batteryType = 'lead';
    public $autonomyDays = 2;

    public $editingSetup = false;
    public $editSetupName = '';
    public $editSystemVoltage = 12;
    public $editInverterEfficiency = 85;
    public $editBatteryType = 'lead';
    public $editAutonomyDays = 2;

    public $currentSetup = null;
    public $showCreateForm = false;




    protected $rules = [
        'newSetupName' => 'required|string|max:255',
        'systemVoltage' => 'required|numeric|min:1',
        'inverterEfficiency' => 'required|numeric|between:50,100',
        'batteryType' => 'required|in:lead,lithium',
        'autonomyDays' => 'required|integer|min:1',
    ];

    public function updated($property)
    {
        $this->validateOnly($property);
    }


    public function mount()
    {
        $this->loadSetups();
    }

    public function getCurrentSetupProperty()
    {
        if (!$this->selectedSetupId) {
            return null;
        }

        return $this->setups->firstWhere('id', (int) $this->selectedSetupId);
    }


    public function loadSetups()
    {
        $this->setups = Auth::user()->powerSetups()->get();
        $this->selectedSetupId = null;
        $this->currentSetup = null;
    }


    public function createSetup()
    {
        $this->validate([
            'newSetupName' => [
                'required|string|max:255',
                Rule::unique('power_setups', 'name')->where(fn ($query) =>
                    $query->where('user_id', Auth::id())
                )
            ],
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

        $this->dispatch('setupChanged', id: $setup->id)->to('appliance-form');


    }

    public function onSelectSetup($id)
    {
        $this->selectedSetupId = $id;
        $this->currentSetup = $this->setups->firstWhere('id', (int) $id);
        $this->dispatch('setupChanged', id: $id);
    }


   public function updatedSelectedSetupId($id)
    {
        $this->currentSetup = $this->setups->firstWhere('id', (int) $id);
        $this->dispatch('setupChanged', id: $id);
    }


    public function render()
    {
        return view('livewire.power-setup-selector');
    }

    public function startEditingSetup()
    {
        if (!$this->selectedSetupId) return;

        $setup = PowerSetup::where('id', $this->selectedSetupId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$setup) return;

        $this->editSetupName = $setup->name;
        $this->editSystemVoltage = $setup->system_voltage;
        $this->editInverterEfficiency = $setup->inverter_efficiency;
        $this->editBatteryType = $setup->battery_type;
        $this->editAutonomyDays = $setup->autonomy_days;
        $this->editingSetup = true;
    }

    public function updateSetup()
    {
        $this->validate([
            'editSetupName' => [
                'required', 'string', 'max:255',
                Rule::unique('power_setups', 'name')->where(fn ($q) =>
                    $q->where('user_id', Auth::id())->where('id', '!=', $this->selectedSetupId)
                )
            ],
            'editSystemVoltage' => 'required|numeric|min:1',
            'editInverterEfficiency' => 'required|numeric|between:50,100',
            'editBatteryType' => 'required|in:lead,lithium',
            'editAutonomyDays' => 'required|integer|min:1',
        ]);

        PowerSetup::where('id', $this->selectedSetupId)
            ->where('user_id', Auth::id())
            ->update([
                'name' => $this->editSetupName,
                'system_voltage' => $this->editSystemVoltage,
                'inverter_efficiency' => $this->editInverterEfficiency,
                'battery_type' => $this->editBatteryType,
                'autonomy_days' => $this->editAutonomyDays,
            ]);

        $this->editingSetup = false;
        $this->loadSetups();
        $this->dispatch('setupChanged', id: $this->selectedSetupId);
    }


}
