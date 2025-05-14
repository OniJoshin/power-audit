<x-sidebar-layout>
    <x-slot name="header">
        Dashboard Overview
    </x-slot>

    <div class="space-y-6">
        {{-- Overview Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white shadow rounded p-4">
                <h3 class="text-sm font-semibold text-gray-600 mb-1">Total Power Setups</h3>
                <p class="text-2xl font-bold text-gray-800">{{ \App\Models\PowerSetup::where('user_id', Auth::id())->count() }}</p>
            </div>

            <div class="bg-white shadow rounded p-4">
                <h3 class="text-sm font-semibold text-gray-600 mb-1">Total Appliances</h3>
                <p class="text-2xl font-bold text-gray-800">{{ \App\Models\Appliance::where('user_id', Auth::id())->count() }}</p>
            </div>

            @if(session('audit_backup_filename'))
            <div class="bg-white shadow rounded p-4">
                <h3 class="text-sm font-semibold text-gray-600 mb-1">Last Backup</h3>
                <a href="{{ route('audit.backup.download') }}" class="text-blue-600 underline text-sm">
                    Download {{ basename(session('audit_backup_filename')) }}
                </a>
            </div>
            @endif
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white shadow rounded p-6 space-y-4">
            <h2 class="text-lg font-semibold text-gray-800">Quick Actions</h2>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('setups.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded font-semibold text-sm">
                    ⚙️ Manage Power Setups
                </a>
                @if(session('selected_setup_id'))
                <a href="{{ route('appliances.index', session('selected_setup_id')) }}" class="bg-green-600 hover:bg-green-700 text-white px-5 py-3 rounded font-semibold text-sm">
                    🔌 View Appliances
                </a>
                <a href="{{ route('summary.index', session('selected_setup_id')) }}" class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-3 rounded font-semibold text-sm">
                    📊 View Power Summary
                </a>
                @endif
                <a href="{{ route('data.index') }}" class="bg-gray-700 hover:bg-gray-800 text-white px-5 py-3 rounded font-semibold text-sm">
                    📁 Import / Export Data
                </a>
            </div>
        </div>
    </div>
</x-sidebar-layout>
