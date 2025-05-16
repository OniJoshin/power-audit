<x-sidebar-layout>
    <x-slot name="header">
        Power Audit Summary
    </x-slot>

    <div class="space-y-4">

        @include('summary.partials.summary-content', [
            'setup' => $setup,
            'applianceAhData' => $applianceAhData,
            'nativeAh' => $nativeAh,
            'inverterAh' => $inverterAh,
            'inefficiency' => $inefficiency,
            'lostAh' => $lostAh,
            'dailyTotalWatts12V' => $dailyTotalWatts12V,
            'dailyTotalWatts230V' => $dailyTotalWatts230V,
            'totalWhWithInverterLoss' => $totalWhWithInverterLoss,
            'totalAh' => $totalAh,
            'recommendedAh' => $recommendedAh,
        ])

    </div>
</x-sidebar-layout>
