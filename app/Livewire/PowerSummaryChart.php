<?php
namespace App\Livewire;

use App\Models\PowerSetup;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PowerSummaryChart extends Component
{
    public $selectedSetupId;
    public $applianceData = [];

    public string $chartType = 'bar';

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

        $this->dispatchChartData(); // ✅ reuse here too
    }


    public function updatedChartType()
    {
        logger('Chart type changed to: ' . $this->chartType);

        if ($this->selectedSetupId && count($this->applianceData)) {
            $this->dispatch('chart-data-updated', data: $this->applianceData, type: $this->chartType);
        }
    }



    public function dispatchChartData()
    {
        $this->dispatch('chart-data-updated', data: $this->applianceData, type: $this->chartType);
    }




    public function render()
    {
        return view('livewire.power-summary-chart');
    }
}
