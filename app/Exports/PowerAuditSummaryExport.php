<?php

// app/Exports/PowerAuditSummaryExport.php

namespace App\Exports;

use App\Models\PowerSetup;
use App\Models\Appliance;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class PowerAuditSummaryExport implements FromArray, WithHeadings, WithTitle
{
    public function array(): array
    {
        $rows = [];

        $setups = PowerSetup::where('user_id', Auth::id())->get();

        foreach ($setups as $setup) {
            $appliances = Appliance::where('power_setup_id', $setup->id)->get();

            $totalWh = $appliances->sum(fn ($a) => $a->watts * $a->hours * $a->quantity);
            $adjustedWh = $totalWh / ($setup->inverter_efficiency / 100); // inverter loss
            $batteryWh = $adjustedWh * $setup->autonomy_days;

            $dailyAh = $setup->system_voltage > 0 ? $totalWh / $setup->system_voltage : 0;
            $adjDailyAh = $setup->system_voltage > 0 ? $adjustedWh / $setup->system_voltage : 0;
            $batteryAh = $setup->system_voltage > 0 ? $batteryWh / $setup->system_voltage : 0;

            $rows[] = [
                $setup->name,
                $appliances->count(),
                round($totalWh, 2),
                round($dailyAh, 2),
                round($adjustedWh, 2),
                round($adjDailyAh, 2),
                $setup->autonomy_days,
                round($batteryWh, 2),
                round($batteryAh, 2),
                $setup->system_voltage . 'V',
                ucfirst($setup->battery_type),
            ];
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Setup Name',
            'Appliance Count',
            'Total Daily Watt-Hours',
            'Daily Ah',
            'Inverter-Adjusted Daily Wh',
            'Inverter-Adjusted Daily Ah',
            'Autonomy Days',
            'Required Battery Wh',
            'Required Battery Ah',
            'Voltage',
            'Battery Type',
        ];

    }

    public function title(): string
    {
        return 'Audit Summary';
    }
}
