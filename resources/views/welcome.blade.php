<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Power Audit</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-700 text-gray-100 antialiased">

    <div class="min-h-screen flex flex-col items-center justify-center text-center px-6 py-12">
        <!-- Logo / App Name -->
        <div class="mb-10">
            <h1 class="text-5xl md:text-6xl font-extrabold text-white tracking-tight">
                ⚡ Power Audit
            </h1>
            <p class="mt-4 text-lg md:text-xl text-gray-300 max-w-2xl mx-auto">
                Calculate your off-grid power needs. Tailored for 12V and 230V systems with inverter efficiency and battery sizing.
            </p>
        </div>

        <!-- Info Box -->
        <div x-data="{ show: false }" class="mb-8">
            <button @click="show = !show"
                    class="bg-blue-600 hover:bg-blue-700 transition text-white px-6 py-2 rounded shadow">
                What is this tool?
            </button>
            <div x-show="show"
                 x-transition
                 class="mt-4 bg-gray-800 p-4 rounded shadow max-w-xl mx-auto text-sm text-gray-300">
                Power Audit helps you estimate daily power consumption, optimize your inverter usage, and size your battery bank with ease — perfect for narrowboats, vans, cabins, or any off-grid setup.
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-4 flex-wrap justify-center">
            <a href="{{ route('login') }}"
               class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded font-semibold shadow transition">
                Get Started
            </a>
            <a href="{{ route('register') }}"
               class="border border-white hover:bg-white hover:text-gray-900 text-white px-6 py-3 rounded font-semibold shadow transition">
                Create an Account
            </a>
        </div>

        <footer class="mt-12 text-sm text-gray-400">
            &copy; {{ date('Y') }} Power Audit. All rights reserved.
        </footer>
    </div>

    @livewireScripts
</body>
</html>
