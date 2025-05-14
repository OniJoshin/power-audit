<?php
// app/Http/Controllers/PowerSetupImportController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\PowerSetupsImport;
use Maatwebsite\Excel\Facades\Excel;

class PowerSetupImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv',
        ]);

        Excel::import(new PowerSetupsImport, $request->file('file'));

        return back()->with('success', 'Power setups imported successfully!');
    }
}
