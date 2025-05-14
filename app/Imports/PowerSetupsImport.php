<?php

// app/Imports/PowerSetupsImport.php

namespace App\Imports;

use App\Models\PowerSetup;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PowerSetupsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['name']) || empty($row['system_voltage'])) {
            return null;
        }

        // Replace (or update) existing setup
        $setup = PowerSetup::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'name' => $row['name'],
            ],
            [
                'system_voltage' => $row['system_voltage'],
                'inverter_efficiency' => $row['inverter_efficiency'] ?? 85,
                'battery_type' => $row['battery_type'] ?? 'lead',
                'autonomy_days' => $row['autonomy_days'] ?? 1,
            ]
        );

        // Remove old appliances for this setup (to be re-imported separately)
        $setup->appliances()->delete();

        return $setup;
    }
}

