<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Appliance;
use Illuminate\Support\Facades\Auth;
use App\Models\PowerSetup;


class ApplianceForm extends Component
{
    public $appliances;
    public $name = '';
    public $voltage = '12';
    public $watts = '';
    public $hours = '';
    public $quantity = 1;
    public $inverterEfficiency = 85; // Default to 85%
    public $systemVoltage = 12; // Default system voltage
    public $batteryType = 'lead'; // or 'lithium'
    public $autonomyDays = 2;
    public $formResetCounter = 0;

    protected $listeners = ['setupChanged' => 'loadSetup'];
    public $selectedSetupId;



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
        $dailyTotalWatts = 0;
        $dailyTotalWatts12V = 0;
        $dailyTotalWatts230V = 0;
        $displayAppliances = [];

        foreach ($this->appliances as $appliance) {
            if (!isset($appliance['watts'], $appliance['hours'], $appliance['quantity'], $appliance['voltage'])) {
                continue; // skip invalid ones
            }

            $watts = $appliance['watts'];
            $hours = $appliance['hours'];
            $qty = $appliance['quantity'];
            $voltage = $appliance['voltage'];

            $baseWh = $watts * $hours * $qty;
            $adjustedWh = ($voltage == '230')
                ? $baseWh / ($this->inverterEfficiency / 100)
                : $baseWh;

            $ah = $this->systemVoltage > 0
                ? $adjustedWh / $this->systemVoltage
                : 0;

            if ($voltage == '230') {
                $dailyTotalWatts230V += $adjustedWh;
            } else {
                $dailyTotalWatts12V += $adjustedWh;
            }

            $dailyTotalWatts += $adjustedWh;

            $displayAppliances[] = array_merge($appliance, [
                'adjustedWh' => round($adjustedWh, 2),
                'ah' => round($ah, 2),
            ]);
        }

        $usablePercent = $this->batteryType === 'lithium' ? 0.9 : 0.5;
        $requiredWh = $dailyTotalWatts * $this->autonomyDays;
        $recommendedAh = $this->systemVoltage > 0
            ? round($requiredWh / ($this->systemVoltage * $usablePercent), 2)
            : 0;

        return view('livewire.appliance-form', [
            'displayAppliances' => $displayAppliances,
            'dailyTotalWatts' => round($dailyTotalWatts, 2),
            'dailyTotalWatts12V' => round($dailyTotalWatts12V, 2),
            'dailyTotalWatts230V' => round($dailyTotalWatts230V, 2),
            'totalWhWithInverterLoss' => round($dailyTotalWatts12V + $dailyTotalWatts230V, 2),
            'totalAh' => round(($dailyTotalWatts12V + $dailyTotalWatts230V) / $this->systemVoltage, 2),
            'recommendedAh' => $recommendedAh,
        ]);
    }

    public function loadSetup($id)
    {
        $this->selectedSetupId = $id;

        $setup = PowerSetup::where('id', $id)
            ->where('user_id', Auth::id())
            ->with('appliances')
            ->firstOrFail();

        $this->systemVoltage = $setup->system_voltage;
        $this->inverterEfficiency = $setup->inverter_efficiency;
        $this->batteryType = $setup->battery_type;
        $this->autonomyDays = $setup->autonomy_days;

        $this->appliances = $setup->appliances->toArray(); // safe conversion
    }




}
