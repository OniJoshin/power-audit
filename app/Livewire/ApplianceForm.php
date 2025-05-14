<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Appliance;
use Illuminate\Support\Facades\Auth;
use App\Models\PowerSetup;


class ApplianceForm extends Component
{
    public $selectedSetupId = null;
    public $editingApplianceId = null;
    public PowerSetup|null $setup = null;



    public $appliances;
    public $name = '';
    public $voltage = '12';
    public $watts = '';
    public $hours = '';
    public $quantity = 1;
    public $formResetCounter = 0;

    public $dailyTotalWatts = 0;
    public $dailyTotalWatts12V = 0;
    public $dailyTotalWatts230V = 0;
    public $totalWhWithInverterLoss = 0;
    public $totalAh = 0;
    public $recommendedAh = 0;
    public $enhancedAppliances = [];

    protected $listeners = ['setupChanged' => 'loadSetup'];


    public function mount()
    {
        $this->loadAppliances();
    }

    public function loadAppliances()
    {
        $this->appliances = Auth::user()->appliances()->get()->toArray();
    }


    public function addAppliance()
    {
        $this->validate([
            'name' => 'required|string',
            'voltage' => 'required|in:12,230',
            'watts' => 'required|numeric|min:0.1',
            'hours' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
        ]);

        if (!$this->selectedSetupId) {
            session()->flash('error', 'Please select a setup first.');
            return;
        }

        Appliance::create([
            'power_setup_id' => $this->selectedSetupId,
            'user_id' => Auth::id(),
            'name' => $this->name,
            'voltage' => $this->voltage,
            'watts' => $this->watts,
            'hours' => $this->hours,
            'quantity' => $this->quantity,
        ]);

        $this->resetForm();
        $this->formResetCounter++;
        $this->loadSetup($this->selectedSetupId); // reload appliances
    }


    public function resetForm()
    {
        $this->editingApplianceId = null;
        $this->name = '';
        $this->voltage = '12';
        $this->watts = '';
        $this->hours = '';
        $this->quantity = 1;
    }


    public function removeAppliance($index)
    {
        $id = $this->appliances[$index]['id'] ?? null;

        if ($id) {
            Appliance::where('id', $id)
                ->where('power_setup_id', $this->selectedSetupId)
                ->where('user_id', Auth::id())
                ->delete();
        }

        $this->loadSetup($this->selectedSetupId);
    }


    public function render()
    {
        $this->calculateTotals();

        return view('livewire.appliance-form');
    }


    public function loadSetup($id)
    {
        if (empty($id)) {
            $this->selectedSetupId = null;
            $this->resetForm();
            $this->setup = null;
            $this->appliances = [];
            return;
        }

        $this->selectedSetupId = $id;

        $this->setup = PowerSetup::where('id', $id)
            ->where('user_id', Auth::id())
            ->with('appliances')
            ->first();

        if (!$this->setup) {
            $this->selectedSetupId = null;
            return;
        }

        $this->appliances = $this->setup->appliances->toArray();
    }



    public function calculateTotals()
    {
         if (!$this->setup) {
            $this->enhancedAppliances = [];
            $this->dailyTotalWatts = 0;
            $this->dailyTotalWatts12V = 0;
            $this->dailyTotalWatts230V = 0;
            $this->totalWhWithInverterLoss = 0;
            $this->totalAh = 0;
            $this->recommendedAh = 0;
            return;
        }
        $systemVoltage = $this->setup->system_voltage;
        $inverterEfficiency = $this->setup->inverter_efficiency;
        $batteryType = $this->setup->battery_type;
        $autonomyDays = $this->setup->autonomy_days;

        $dailyTotalWatts = 0;
        $dailyTotalWatts12V = 0;
        $dailyTotalWatts230V = 0;
        $enhancedAppliances = [];

        foreach ($this->appliances as $appliance) {
            $watts = $appliance['watts'];
            $hours = $appliance['hours'];
            $qty = $appliance['quantity'];
            $voltage = $appliance['voltage'];

            $baseWh = $watts * $hours * $qty;
            $adjustedWh = $voltage == '230'
                ? $baseWh / ($inverterEfficiency / 100)
                : $baseWh;

            $ah = $systemVoltage > 0 ? $adjustedWh / $systemVoltage : 0;

            if ($voltage == '230') {
                $dailyTotalWatts230V += $adjustedWh;
            } else {
                $dailyTotalWatts12V += $adjustedWh;
            }

            $dailyTotalWatts += $adjustedWh;

            $enhancedAppliances[] = array_merge($appliance, [
                'adjustedWh' => round($adjustedWh, 2),
                'ah' => round($ah, 2),
            ]);
        }

        $usablePercent = $batteryType === 'lithium' ? 0.9 : 0.5;
        $requiredWh = $dailyTotalWatts * $autonomyDays;
        $recommendedAh = $systemVoltage > 0
            ? round($requiredWh / ($systemVoltage * $usablePercent), 2)
            : 0;

        // Assign results to component properties
        $this->enhancedAppliances = $enhancedAppliances;
        $this->dailyTotalWatts = round($dailyTotalWatts, 2);
        $this->dailyTotalWatts12V = round($dailyTotalWatts12V, 2);
        $this->dailyTotalWatts230V = round($dailyTotalWatts230V, 2);
        $this->totalWhWithInverterLoss = round($dailyTotalWatts12V + $dailyTotalWatts230V, 2);
        $this->totalAh = $systemVoltage > 0 ? round($this->totalWhWithInverterLoss / $systemVoltage, 2) : 0;
        $this->recommendedAh = $recommendedAh;
    }

    public function editAppliance($id)
    {
        $appliance = Appliance::where('id', $id)
            ->where('power_setup_id', $this->selectedSetupId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $this->editingApplianceId = $appliance->id;
        $this->name = $appliance->name;
        $this->voltage = $appliance->voltage;
        $this->watts = $appliance->watts;
        $this->hours = $appliance->hours;
        $this->quantity = $appliance->quantity;
    }

    public function updateAppliance()
    {
        $this->validate([
            'name' => 'required|string',
            'voltage' => 'required|in:12,230',
            'watts' => 'required|numeric|min:0.1',
            'hours' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
        ]);

        Appliance::where('id', $this->editingApplianceId)
            ->where('power_setup_id', $this->selectedSetupId)
            ->where('user_id', Auth::id())
            ->update([
                'name' => $this->name,
                'voltage' => $this->voltage,
                'watts' => $this->watts,
                'hours' => $this->hours,
                'quantity' => $this->quantity,
            ]);

        $this->resetForm();
        $this->formResetCounter++;
        $this->loadSetup($this->selectedSetupId);
    }


}
