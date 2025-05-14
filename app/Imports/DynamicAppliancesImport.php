<?php
// app/Imports/DynamicAppliancesImport.php

namespace App\Imports;

use App\Models\Appliance;
use App\Models\PowerSetup;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Collection;

class DynamicAppliancesImport implements ToCollection, WithHeadingRow, WithTitle
{
    protected string $title;

    public function title(): string
    {
        return $this->title ?? '';
    }

    public function collection(Collection $rows)
    {
        $setup = PowerSetup::where('user_id', Auth::id())
            ->where('name', $this->title())
            ->first();

        if (!$setup) {
            return;
        }

        foreach ($rows as $row) {
            if (empty($row['name']) || $row['name'] === 'Total (Wh)') continue;

            Appliance::create([
                'user_id' => Auth::id(),
                'power_setup_id' => $setup->id,
                'name' => $row['name'],
                'voltage' => $row['voltage'] ?? 0,
                'watts' => $row['watts'] ?? 0,
                'hours' => $row['hours_per_day'] ?? $row['hours'] ?? 0,
                'quantity' => $row['quantity'] ?? 1,
            ]);
        }
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }
}
