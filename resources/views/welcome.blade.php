<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Power Audit</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900 antialiased">

    <div class="min-h-screen flex flex-col justify-center items-center px-4">
        <div class="max-w-3xl w-full text-center">
            <h1 class="text-5xl font-extrabold text-blue-600 mb-6">
                Power Audit
            </h1>
            <p class="text-lg text-gray-700 mb-6">
                Easily calculate your daily power usage and find the right battery setup for your off-grid lifestyle.
            </p>

            <div x-data="{ showInfo: false }" class="mb-6">
                <button
                    @click="showInfo = !showInfo"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded transition"
                >
                    What is this?
                </button>

                <div x-show="showInfo" class="mt-4 bg-white shadow p-4 rounded">
                    <p>
                        This tool helps you audit the power consumption of your 12V and 230V appliances,
                        accounts for inverter efficiency, and recommends appropriate battery bank sizes.
                    </p>
                </div>
            </div>

            <a
                href="{{ route('login') }}"
                class="inline-block mt-4 bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded font-semibold transition"
            >
                Get Started
            </a>
        </div>
    </div>

    @livewireScripts
</body>
</html>
