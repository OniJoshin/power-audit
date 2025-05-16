{{-- File: resources/views/layouts/sidebar.blade.php --}}

<x-app-layout>
    <div class="flex">
        <!-- Sidebar -->
        <aside class="bg-gray-900 text-gray-100 w-64 min-h-screen px-4 py-6 flex flex-col space-y-6">
            <!-- App Name / Logo -->
            <div class="text-xl font-bold tracking-wide text-white">
                ⚡ Power Audit
            </div>

            <!-- Navigation -->
            <nav class="flex-1 space-y-4">
                <!-- Main Section -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-400 uppercase mb-2">Main</h4>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('dashboard') }}"
                            class="flex items-center px-3 py-2 rounded-md hover:bg-gray-800 {{ request()->routeIs('dashboard') ? 'bg-gray-800' : '' }}">
                                <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('setups.index') }}"
                            class="flex items-center px-3 py-2 rounded-md hover:bg-gray-800 {{ request()->routeIs('setups.index') ? 'bg-gray-800' : '' }}">
                                <svg class="w-5 h-5 mr-2 text-green-400" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24"><path d="M3 7h18M3 12h18M3 17h18" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                Power Setups
                            </a>
                        </li>
                        @php
                            $selectedSetupId = session('selected_setup_id');
                        @endphp

                        @if($selectedSetupId)
                            <li>
                                <a href="{{ route('appliances.index', $selectedSetupId) }}"
                                class="flex items-center px-3 py-2 rounded-md hover:bg-gray-800 {{ request()->routeIs('appliances.index') ? 'bg-gray-800' : '' }}">
                                    <svg class="w-5 h-5 mr-2 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path d="M9 17v-2a4 4 0 014-4h2a4 4 0 014 4v2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <circle cx="9" cy="7" r="4" />
                                    </svg>
                                    Appliances
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('power-summary.show', $selectedSetupId) }}"
                                class="flex items-center px-3 py-2 rounded-md hover:bg-gray-800 {{ request()->routeIs('power-summary.show') ? 'bg-gray-800' : '' }}">
                                    <svg class="w-5 h-5 mr-2 text-cyan-400" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path d="M11 3h10M9 7h12M5 11h16M3 15h18M7 19h14" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    Power Summary
                                </a>
                            </li>

                        @endif

                        <li>
                            <a href="{{ route('data.index') }}"
                            class="flex items-center px-3 py-2 rounded-md hover:bg-gray-800 {{ request()->routeIs('data.index') ? 'bg-gray-800' : '' }}">
                                <svg class="w-5 h-5 mr-2 text-yellow-400" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 4h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                Data & Import
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Other -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-400 uppercase mb-2">Account</h4>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('profile.edit') }}"
                            class="flex items-center px-3 py-2 rounded-md hover:bg-gray-800 {{ request()->routeIs('profile.edit') ? 'bg-gray-800' : '' }}">
                                <svg class="w-5 h-5 mr-2 text-purple-400" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24"><path d="M5.121 17.804A10.966 10.966 0 0012 20c2.21 0 4.262-.664 5.879-1.796M15 11a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                Profile
                            </a>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center px-3 py-2 rounded-md hover:bg-gray-800 text-left">
                                    <svg class="w-5 h-5 mr-2 text-red-400" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 11-4 0v-1m0-8v-1a2 2 0 114 0v1" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    Log Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
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
