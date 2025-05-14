<?php

// app/Exports/AllAppliancesExport.php

namespace App\Exports;

use App\Models\PowerSetup;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AllAppliancesExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        $sheets = [];

        $setups = PowerSetup::where('user_id', Auth::id())->get();

        foreach ($setups as $setup) {
            $sheets[] = new AppliancesExport($setup->id, $setup->name, $setup->system_voltage);
        }

        // Append the Power Setups summary sheet
        $sheets[] = new PowerSetupsExport();
        $sheets[] = new PowerAuditSummaryExport();

        return $sheets;
    }
}

