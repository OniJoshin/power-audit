<?php
namespace App\Livewire;

use App\Models\PowerSetup;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PowerSummaryChart extends Component
{
    public $selectedSetupId;
    public $applianceData = [];

    protected $listeners = ['setupChanged' => 'loadChartData'];

    public function loadChartData($id)
    {
        $this->selectedSetupId = $id;

        $setup = PowerSetup::where('id', $id)
            ->where('user_id', Auth::id())
            ->with('appliances')
            ->first();

        if (!$setup) {
            $this->applianceData = [];
            return;
        }

        $this->applianceData = $setup->appliances->map(function ($appliance) use ($setup) {
            $baseWh = $appliance->watts * $appliance->hours * $appliance->quantity;
            $adjustedWh = $appliance->voltage == 230
                ? $baseWh / ($setup->inverter_efficiency / 100)
                : $baseWh;

            return [
                'name' => $appliance->name,
                'wh' => round($adjustedWh, 2),
            ];
        })->toArray();
    }

    public function render()
    {
        return view('livewire.power-summary-chart');
    }
}
