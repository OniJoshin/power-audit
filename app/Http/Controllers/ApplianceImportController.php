<?php

// app/Http/Controllers/ApplianceImportController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\AppliancesImport;
use Maatwebsite\Excel\Facades\Excel;

class ApplianceImportController extends Controller
{
    public function import(Request $request, $setupId)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv',
        ]);

        Excel::import(new AppliancesImport($setupId), $request->file('file'));

        return back()->with('success', 'Appliances imported successfully!');
    }
}

