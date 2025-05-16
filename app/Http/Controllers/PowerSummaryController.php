<?php

namespace App\Http\Controllers;

use App\Models\PowerSetup;
use Illuminate\Support\Facades\Auth;

class PowerSummaryController extends Controller
{
    public function show($setupId)
    {
        $setup = PowerSetup::with('appliances')
            ->where('id', $setupId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $applianceAhData = [];
        $nativeAh = 0;
        $inverterAh = 0;
        $lostAh = 0;

        foreach ($setup->appliances as $appliance) {
            $baseWh = $appliance->watts * $appliance->hours * $appliance->quantity;

            if ($appliance->voltage == 230) {
                $adjustedWh = $baseWh / ($setup->inverter_efficiency / 100);
                $lostWh = $adjustedWh - $baseWh;
                $lostAh += $lostWh / $setup->system_voltage;
                $inverterAh += $adjustedWh / $setup->system_voltage;
            } else {
                $nativeAh += $baseWh / $setup->system_voltage;
            }

            $ah = ($appliance->voltage == 230 ? $adjustedWh : $baseWh) / $setup->system_voltage;
            $applianceAhData[] = [
                'name' => $appliance->name,
                'ah' => round($ah, 2),
            ];
        }

        $totalAh = $nativeAh + $inverterAh;
        $inefficiency = $totalAh > 0 ? round(($lostAh / $totalAh) * 100) : 0;

        $dailyTotalWatts12V = round($nativeAh * $setup->system_voltage, 2);
        $dailyTotalWatts230V = round(($inverterAh - $lostAh) * $setup->system_voltage, 2);
        $totalWhWithInverterLoss = $dailyTotalWatts12V + $dailyTotalWatts230V;
        $totalAhRounded = round($totalWhWithInverterLoss / $setup->system_voltage, 2);

        $usablePercent = $setup->battery_type === 'lithium' ? 0.9 : 0.5;
        $requiredWh = $totalWhWithInverterLoss * $setup->autonomy_days;
        $recommendedAh = round($requiredWh / ($setup->system_voltage * $usablePercent), 2);

        return view('summary.index', [
            'setup' => $setup,
            'applianceAhData' => $applianceAhData,
            'nativeAh' => round($nativeAh, 2),
            'inverterAh' => round($inverterAh, 2),
            'inefficiency' => $inefficiency,
            'lostAh' => round($lostAh, 2),
            'dailyTotalWatts12V' => $dailyTotalWatts12V,
            'dailyTotalWatts230V' => $dailyTotalWatts230V,
            'totalWhWithInverterLoss' => $totalWhWithInverterLoss,
            'totalAh' => $totalAhRounded,
            'recommendedAh' => $recommendedAh,
        ]);
    }
}
