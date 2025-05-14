<?php

// app/Exports/PowerSetupsExport.php

namespace App\Exports;

use App\Models\PowerSetup;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class PowerSetupsExport implements FromArray, WithHeadings, WithTitle
{
    public function array(): array
    {
        return PowerSetup::where('user_id', Auth::id())
            ->get()
            ->map(function ($setup) {
                return [
                    $setup->name,
                    $setup->system_voltage,
                    $setup->inverter_efficiency,
                    ucfirst($setup->battery_type),
                    $setup->autonomy_days,
                    $setup->created_at->format('Y-m-d'),
                ];
            })
            ->toArray();
    }

    public function headings(): array
    {
        return ['Name', 'Voltage (V)', 'Inverter Efficiency (%)', 'Battery Type', 'Autonomy Days', 'Created'];
    }

    public function title(): string
    {
        return 'Power Setups';
    }
}
