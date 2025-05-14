<?php
namespace App\Exports;

use App\Models\Appliance;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class AppliancesExport implements FromArray, WithHeadings, WithTitle
{
    protected $setupId;
    protected $title;
    protected $systemVoltage;

    public function __construct($setupId, $title = 'Setup', $systemVoltage = 12)
    {
        $this->setupId = $setupId;
        $this->title = $title;
        $this->systemVoltage = $systemVoltage;
    }


    public function title(): string
    {
        return $this->title;
    }

    public function headings(): array
    {
        return ['Name', 'Voltage (V)', 'Watts', 'Hours per Day', 'Quantity', 'Daily Watt-Hours', 'Daily Amp-Hours'];
    }

    public function array(): array
    {
        $appliances = Appliance::where('user_id', Auth::id())
                        ->where('power_setup_id', $this->setupId)
                        ->get();

        $rows = [];
        $totalWh = 0;
        $totalAh = 0;

        foreach ($appliances as $a) {
            $dailyWh = $a->watts * $a->hours * $a->quantity;
            $dailyAh = $this->systemVoltage > 0 ? $dailyWh / $this->systemVoltage : 0;
            $totalWh += $dailyWh;
            $totalAh += $dailyAh;

            $rows[] = [
                $a->name,
                $a->voltage,
                $a->watts,
                $a->hours,
                $a->quantity,
                round($dailyWh, 2),
                round($dailyAh, 2),
            ];
        }

        $rows[] = ['', '', '', '', 'Totals', round($totalWh, 2),round($totalAh, 2),];

        return $rows;
    }
}
