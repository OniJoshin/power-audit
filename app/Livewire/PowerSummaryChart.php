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

    public $dailyTotalWatts12V = 0;
    public $dailyTotalWatts230V = 0;
    public $totalWhWithInverterLoss = 0;
    public $totalAh = 0;
    public $recommendedAh = 0;
    public $setup;


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

        // ✅ These are the lines you're missing:
        $this->dailyTotalWatts12V = round($nativeAh * $setup->system_voltage, 2);
        $this->dailyTotalWatts230V = round(($inverterAh - $lostAh) * $setup->system_voltage, 2);
        $this->totalWhWithInverterLoss = $this->dailyTotalWatts12V + $this->dailyTotalWatts230V;
        $this->totalAh = round($this->totalWhWithInverterLoss / $setup->system_voltage, 2);

        $usablePercent = $setup->battery_type === 'lithium' ? 0.9 : 0.5;
        $requiredWh = $this->totalWhWithInverterLoss * $setup->autonomy_days;
        $this->recommendedAh = round($requiredWh / ($setup->system_voltage * $usablePercent), 2);

        $this->setup = $setup;
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
        // If there's no setup yet, we avoid errors
        $inverterAh = 0;
        $nativeAh = 0;
        $lostAh = 0;
        $inefficiency = 0;

        if ($this->setup) {
            foreach ($this->setup->appliances as $appliance) {
                $baseWh = $appliance->watts * $appliance->hours * $appliance->quantity;
                if ($appliance->voltage == 230) {
                    $adjustedWh = $baseWh / ($this->setup->inverter_efficiency / 100);
                    $lostAh += ($adjustedWh - $baseWh) / $this->setup->system_voltage;
                    $inverterAh += $adjustedWh / $this->setup->system_voltage;
                } else {
                    $nativeAh += $baseWh / $this->setup->system_voltage;
                }
            }

            $totalAh = $nativeAh + $inverterAh;
            $inefficiency = $totalAh > 0 ? round(($lostAh / $totalAh) * 100) : 0;
        }

        return view('livewire.power-summary-chart', [
            'applianceAhData' => $this->applianceData,
            'nativeAh' => round($nativeAh, 2),
            'inverterAh' => round($inverterAh, 2),
            'inefficiency' => $inefficiency,
            'lostAh' => round($lostAh, 2),
        ]);
    }

}
