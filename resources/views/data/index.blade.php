{{-- File: resources/views/data/index.blade.php --}}

<x-sidebar-layout>
    <x-slot name="header">Import & Export Data</x-slot>

    <div class="space-y-6">
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Import Setups</h2>
            <form action="{{ route('setups.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" accept=".csv,.xlsx" required>
                <button type="submit" class="ml-2 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Import Setups
                </button>
            </form>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Import Full Power Audit</h2>
            <form action="{{ route('audit.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" accept=".xlsx" required>
                <label class="ml-2">
                    <input type="checkbox" name="dry_run" value="1"> Dry-run (preview only)
                </label>
                <button type="submit" class="ml-2 bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Import Full Audit
                </button>
            </form>

            @if(session('audit_backup_filename'))
                <div class="mt-4">
                    <a href="{{ route('audit.backup.download') }}" class="text-blue-600 underline hover:text-blue-800">
                        Download Backup from Last Import
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-sidebar-layout>
