<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PowerSetup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;

class PdfExportController extends Controller
{
    public function export(PowerSetup $setup)
    {
        $setup->load('appliances');

        // Run same calculation logic as Livewire
        $inverterEfficiency = $setup->inverter_efficiency;
        $voltage = $setup->system_voltage;
        $nativeAh = 0;
        $inverterAh = 0;
        $lostAh = 0;

        $appliances = $setup->appliances->map(function ($a) use (&$nativeAh, &$inverterAh, &$lostAh, $inverterEfficiency, $voltage) {
            $baseWh = $a->watts * $a->hours * $a->quantity;

            if ($a->voltage == 230) {
                $adjustedWh = $baseWh / ($inverterEfficiency / 100);
                $lostWh = $adjustedWh - $baseWh;
                $lostAh += $lostWh / $voltage;
                $inverterAh += $adjustedWh / $voltage;
            } else {
                $nativeAh += $baseWh / $voltage;
            }

            return [
                'name' => $a->name,
                'voltage' => $a->voltage,
                'watts' => $a->watts,
                'hours' => $a->hours,
                'quantity' => $a->quantity,
                'wh' => round($baseWh, 2),
                'ah' => round(($a->voltage == 230 ? $adjustedWh : $baseWh) / $voltage, 2),
            ];
        });

        $totalAh = $nativeAh + $inverterAh;
        $inefficiencyPercent = $totalAh > 0 ? round(($lostAh / $totalAh) * 100) : 0;
        $recommendedAh = $totalAh / ($setup->battery_type === 'lithium' ? 0.9 : 0.5) * $setup->autonomy_days;

        $chartBase64 = session('chart_image');
        $inverterBase64 = session('inverter_image');

        $pdf = Pdf::loadView('pdf.report', [
            'setup' => $setup,
            'appliances' => $appliances,
            'nativeAh' => round($nativeAh, 2),
            'inverterAh' => round($inverterAh, 2),
            'lostAh' => round($lostAh, 2),
            'inefficiencyPercent' => $inefficiencyPercent,
            'recommendedAh' => round($recommendedAh, 2),
            'chartBase64' => $chartBase64,
            'inverterBase64' => $inverterBase64,
        ]);

        // Return and then forget the session data
        session()->forget(['chart_image', 'inverter_image']);
        return $pdf->download('power-audit-report.pdf');
    }
}
