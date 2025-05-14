<?php

// app/Imports/AppliancesImport.php

namespace App\Imports;

use App\Models\Appliance;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AppliancesImport implements ToModel, WithHeadingRow
{
    protected $setupId;

    public function __construct($setupId)
    {
        $this->setupId = $setupId;
    }

    public function model(array $row)
    {
        // Skip invalid or summary rows
        if (empty($row['name']) || $row['name'] === 'Total (Wh)') {
            return null;
        }

        return new Appliance([
            'user_id' => Auth::id(),
            'power_setup_id' => $this->setupId,
            'name' => $row['name'],
            'voltage' => $row['voltage'] ?? 0,
            'watts' => $row['watts'] ?? 0,
            'hours' => $row['hours_per_day'] ?? $row['hours'] ?? 0,
            'quantity' => $row['quantity'] ?? 1,
        ]);
    }
}
