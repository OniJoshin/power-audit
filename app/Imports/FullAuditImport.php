<?php

// app/Imports/FullAuditImport.php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FullAuditImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        // We'll match sheets dynamically during runtime
        return [
            'Power Setups' => new \App\Imports\PowerSetupsImport,
            '*' => new \App\Imports\DynamicAppliancesImport,
        ];
    }
}

/*You'll also need to register the sheet title from the FullAuditImport context — this workaround depends on how Maatwebsite handles wildcards (if needed, we can replace it with a factory or explicit mapping).*/
