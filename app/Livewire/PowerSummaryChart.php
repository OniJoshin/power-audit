<?php
namespace App\Livewire;

use App\Models\PowerSetup;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PowerSummaryChart extends Component
{
    public $selectedSetupId;
    public $applianceData = [];

    public string $chartType = 'pie';

    protected $listeners = ['setupChanged' => 'loadChartData','chartImageCaptured' => 'storeChartImage', 'inverterImageCaptured' => 'storeInverterImage',];

    public string $chartImage = '';
    public string $inverterImage = '';
    public bool $canDownloadPdf = false;

    public function mount($selectedSetupId = null)
    {
        if ($selectedSetupId) {
            $this->loadChartData($selectedSetupId);
        }
    }


    public function storeChartImage(string $image)
    {
        session(['chart_image' => $image]);
         $this->checkIfImagesReady();
    }

    public function storeInverterImage(string $image)
    {
        session(['inverter_image' => $image]);
         $this->checkIfImagesReady();
    }

    public function checkIfImagesReady()
    {
        if (session('chart_image') && session('inverter_image')) {
            $this->canDownloadPdf = true;
        }
    }

    public function loadChartData($id)
    {
        $nativeAh = 0;
        $inverterAh = 0;
        $lostAh = 0;

        $this->selectedSetupId = $id;

        $setup = PowerSetup::where('id', $id)
            ->where('user_id', Auth::id())
            ->with('appliances')
            ->first();

        if (!$setup) {
            $this->applianceData = [];
            return;
        }

        $this->applianceData = $setup->appliances->map(function ($appliance) use ($setup, &$nativeAh, &$inverterAh, &$lostAh) {
            $baseWh = $appliance->watts * $appliance->hours * $appliance->quantity;

            if ($appliance->voltage == 230) {
                $adjustedWh = $baseWh / ($setup->inverter_efficiency / 100);
                $lostWh = $adjustedWh - $baseWh;
                $lostAh += $lostWh / $setup->system_voltage;
                $inverterAh += $adjustedWh / $setup->system_voltage;
            } else {
                $nativeAh += $baseWh / $setup->system_voltage;
            }

            return [
                'name' => $appliance->name,
                'ah' => round(($appliance->voltage == 230 ? $adjustedWh : $baseWh) / $setup->system_voltage, 2),
            ];
        })->toArray();

        $totalAh = $nativeAh + $inverterAh;
        $inefficiencyPercent = $totalAh > 0 ? round(($lostAh / $totalAh) * 100) : 0;

        $this->dispatch('chart-data-updated', data: $this->applianceData, type: $this->chartType);

        $this->dispatch('load-inverter-native-data',
            native: round($nativeAh, 2),
            inverter: round($inverterAh, 2),
            inefficiency: $inefficiencyPercent,
            lostAh: round($lostAh, 2),
        );
    }


    public function updatedChartType()
    {
        logger('Chart type changed to: ' . $this->chartType);

        if ($this->selectedSetupId && count($this->applianceData)) {
            $this->dispatch('chart-data-updated', data: $this->applianceData, type: $this->chartType);
        }
    }





    public function render()
    {
        return view('livewire.power-summary-chart');
    }
}
