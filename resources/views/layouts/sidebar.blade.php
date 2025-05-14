{{-- File: resources/views/layouts/sidebar.blade.php --}}

<x-app-layout>
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-gray-200 min-h-screen px-4 py-6">
            <nav class="space-y-2">
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    Dashboard
                </x-nav-link>

                <x-nav-link :href="route('setups.index')" :active="request()->routeIs('setups.index')">
                    Power Setups
                </x-nav-link>

                {{-- Appliances and Summary only shown if a setup is selected --}}
                @if(session('selected_setup_id'))
                    <x-nav-link :href="route('appliances.index', session('selected_setup_id'))" :active="request()->routeIs('appliances.index')">
                        Appliances
                    </x-nav-link>

                    <x-nav-link :href="route('summary.index', session('selected_setup_id'))" :active="request()->routeIs('summary.index')">
                        Power Summary
                    </x-nav-link>
                @endif

                <x-nav-link :href="route('data.index')" :active="request()->routeIs('data.index')">
                    Import / Export
                </x-nav-link>

                <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                    Profile
                </x-nav-link>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 px-6 py-6">
            <h1 class="text-xl font-semibold text-gray-800 mb-4">
                {{ $header ?? 'Power Audit' }}
            </h1>

            {{ $slot }}
        </div>
    </div>
</x-app-layout>
