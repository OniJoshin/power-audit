<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfExportController;
use App\Exports\AppliancesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AllAppliancesExport;
use App\Models\PowerSetup;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/pdf/{setup}', [PdfExportController::class, 'export'])->name('pdf.export');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/export/appliances/all', function () {
    return Excel::download(new AllAppliancesExport(), 'all_appliances.xlsx');
})->middleware('auth');
Route::get('/export/appliances/{setup}', function ($setupId) {
    return Excel::download(new AppliancesExport($setupId), 'appliances.csv');
})->middleware(['auth']);


Route::post('/import/setups', [App\Http\Controllers\PowerSetupImportController::class, 'import'])
     ->name('setups.import')
     ->middleware('auth');
Route::post('/import/full-audit', [\App\Http\Controllers\FullAuditImportController::class, 'import'])
     ->name('audit.import')
     ->middleware('auth');
Route::post('/import/appliances/{setup}', [App\Http\Controllers\ApplianceImportController::class, 'import'])
     ->name('appliances.import')
     ->middleware('auth');

Route::get('/import/review', function () {
    if (!session()->has('audit_dry_run_data')) {
        return redirect()->back()->with('error', 'No dry-run data found.');
    }

    $existingSetupNames = PowerSetup::where('user_id', Auth::id())
        ->pluck('name')
        ->map(fn ($name) => strtolower(trim($name)))
        ->toArray();

    return view('audit.review', [
        'sheets' => session('audit_dry_run_data'),
        'timestamp' => session('audit_dry_run_timestamp'),
        'existingSetups' => $existingSetupNames,
    ]);
})->name('audit.review')->middleware('auth');

Route::get('/download/latest-backup', function () {
    $filename = session('audit_backup_filename');

    if (!$filename || !Storage::exists($filename)) {
        return redirect()->back()->with('error', 'Backup file not found.');
    }

    return Storage::download($filename);
})->middleware('auth')->name('audit.backup.download');




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



require __DIR__.'/auth.php';
