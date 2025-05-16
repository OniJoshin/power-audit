<x-sidebar-layout>
    <x-slot name="header">
        Welcome back, {{ Auth::user()->name }}
    </x-slot>

    <div class="space-y-8">

        {{-- Overview Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-dashboard.card
                title="Total Power Setups"
                :value="\App\Models\PowerSetup::where('user_id', Auth::id())->count()"
                icon="⚡"
                color="blue"
            />

            <x-dashboard.card
                title="Total Appliances"
                :value="\App\Models\Appliance::where('user_id', Auth::id())->count()"
                icon="🔌"
                color="green"
            />

            @if(session('audit_backup_filename'))
                <div class="bg-white shadow rounded p-4">
                    <h3 class="text-sm font-semibold text-gray-600 mb-1">Last Backup</h3>
                    <a href="{{ route('audit.backup.download') }}"
                       class="text-blue-600 underline text-sm">
                        Download {{ basename(session('audit_backup_filename')) }}
                    </a>
                </div>
            @endif
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white shadow rounded p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h2>

            <div class="flex flex-wrap gap-4">
                <x-dashboard.action-button
                    route="setups.index"
                    label="Manage Power Setups"
                    icon="⚙️"
                    color="blue"
                />

                @if(session('selected_setup_id'))
                    <x-dashboard.action-button
                        :route="route('appliances.index', session('selected_setup_id'))"
                        label="View Appliances"
                        icon="🔌"
                        color="green"
                    />

                    <x-dashboard.action-button
                        :route="route('summary.index', session('selected_setup_id'))"
                        label="View Power Summary"
                        icon="📊"
                        color="purple"
                        :class="'bg-purple-600 hover:bg-purple-700'"
                    />
                @endif

                <x-dashboard.action-button
                    route="data.index"
                    label="Import / Export Data"
                    icon="📁"
                    color="gray"
                    :class="'bg-gray-600 hover:bg-gray-700'"
                />
            </div>
        </div>

    </div>
</x-sidebar-layout>
