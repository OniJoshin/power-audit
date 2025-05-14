<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\FullAuditImport;
use App\Exports\AllAppliancesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class FullAuditImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx',
        ]);

        $userId = Auth::id();
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $tempPath = "tmp/user_{$userId}_audit_upload.xlsx";

        $confirmedImport = $request->input('confirmed_import');
        $dryRun = $request->input('dry_run');

        // ✅ Step 1: Handle confirmed import
        if ($confirmedImport) {
            if (!Storage::exists($tempPath)) {
                return redirect()->route('audit.review')->with('error', 'Temp import file not found. Please re-upload.');
            }

            Excel::import(new FullAuditImport(), storage_path("app/{$tempPath}"));

            // Clean up
            Storage::delete($tempPath);
            session()->forget(['audit_dry_run_data', 'audit_dry_run_timestamp']);

            return redirect()->route('dashboard')->with('success', 'Import confirmed and completed.');
        }

        // ✅ Step 2: Dry-run preview
        if ($dryRun) {
            // Save file to tmp location
            $request->file('file')->storeAs('tmp', "user_{$userId}_audit_upload.xlsx");

            $sheets = Excel::toArray([], $request->file('file'));

            session([
                'audit_dry_run_data' => $sheets,
                'audit_dry_run_timestamp' => now()->toDateTimeString(),
            ]);

            return redirect()->route('audit.review');
        }

        // ✅ Step 3: Backup and full import
        $backupFilename = "backups/power_audit_backup_user_{$userId}_{$timestamp}.xlsx";
        Excel::store(new AllAppliancesExport(), $backupFilename);
        session()->put('audit_backup_filename', $backupFilename);


        Excel::import(new FullAuditImport(), $request->file('file'));

        return back()->with('success', 'Import complete. Backup saved to storage/backups.');
    }
}
