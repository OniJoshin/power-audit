<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PdfExportController;
use App\Http\Controllers\PowerSetupImportController;
use App\Http\Controllers\ApplianceImportController;
use App\Http\Controllers\FullAuditImportController;
use App\Http\Controllers\Auth\LogoutController;
use App\Exports\AppliancesExport;
use App\Exports\AllAppliancesExport;
use App\Models\PowerSetup;
use Maatwebsite\Excel\Facades\Excel;

// ─────────────────────────────────────────────
// Public Routes
// ─────────────────────────────────────────────

Route::get('/', function () {
    return view('welcome');
});

// ─────────────────────────────────────────────
// Authenticated Routes
// ─────────────────────────────────────────────

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard (home)
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    // Power Setups
    Route::view('/setups', 'setups.index')->name('setups.index');
    Route::get('/setups/{id}/appliances', fn ($id) => view('appliances.index', ['selectedSetupId' => $id]))
        ->name('appliances.index');
    Route::get('/setups/{id}/summary', fn ($id) => view('summary.index', ['selectedSetupId' => $id]))
        ->name('summary.index');

    // Import/Export UI
    Route::view('/data', 'data.index')->name('data.index');

    // PDF Export
    Route::get('/pdf/{setup}', [PdfExportController::class, 'export'])->name('pdf.export');

    // Excel/CSV Exports
    Route::get('/export/appliances/all', fn () => Excel::download(new AllAppliancesExport(), 'all_appliances.xlsx'));
    Route::get('/export/appliances/{setup}', fn ($setupId) => Excel::download(new AppliancesExport($setupId), 'appliances.csv'));

    // Imports
    Route::post('/import/setups', [PowerSetupImportController::class, 'import'])->name('setups.import');
    Route::post('/import/appliances/{setup}', [ApplianceImportController::class, 'import'])->name('appliances.import');
    Route::post('/import/full-audit', [FullAuditImportController::class, 'import'])->name('audit.import');

    // Dry-run review
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
    })->name('audit.review');

    // Backup download
    Route::get('/download/latest-backup', function () {
        $filename = session('audit_backup_filename');
        if (!$filename || !Storage::exists($filename)) {
            return redirect()->back()->with('error', 'Backup file not found.');
        }
        return Storage::download($filename);
    })->name('audit.backup.download');

    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });
});

// ─────────────────────────────────────────────
// Logout
// ─────────────────────────────────────────────

Route::post('/logout', [LogoutController::class, 'destroy'])->name('logout');

require __DIR__.'/auth.php';
