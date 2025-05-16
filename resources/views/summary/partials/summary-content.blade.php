<div class="w-full max-w-6xl mx-auto space-y-12">

    <!-- Header Row -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-semibold text-gray-800">Power Summary</h2>

        {{-- Optionally include PDF download if charts are ready (control this with session or a condition) --}}
       <div class="flex justify-end">
            <a id="pdfButton" href="{{ route('pdf.export', $setup->id) }}"
            target="_blank"
            class="hidden inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                Download PDF Report
            </a>
            <p id="pdfStatus" class="text-gray-500 italic">Preparing charts… please wait.</p>
        </div>
    </div>

    <!-- Summary Grid -->
    <div class="grid md:grid-cols-2 gap-6">
        <!-- Daily Usage Card -->
        <div class="bg-white p-6 rounded-xl shadow border">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Daily Usage Summary</h3>
            <ul class="space-y-2 text-sm text-gray-600">
                <li><strong>12V Items:</strong> {{ $dailyTotalWatts12V }} Wh</li>
                <li><strong>230V Items (Adjusted):</strong> {{ $dailyTotalWatts230V }} Wh</li>
                <li><strong>Total Daily Usage:</strong> {{ $totalWhWithInverterLoss }} Wh</li>
                <li><strong>Total Ah (@ {{ $setup->system_voltage }}V):</strong> {{ $totalAh }} Ah</li>
            </ul>
        </div>

        <!-- Battery Bank Recommendation -->
        <div class="bg-yellow-50 border border-yellow-200 p-6 rounded-xl shadow">
            <h3 class="text-lg font-semibold text-yellow-800 mb-3">Recommended Battery Bank</h3>
            <p class="text-sm text-gray-700">
                For <strong>{{ $setup->autonomy_days }}</strong> day(s) autonomy using
                <strong>{{ ucfirst($setup->battery_type) }}</strong> batteries:
            </p>
            <p class="text-2xl font-bold text-yellow-900 mt-3">
                → {{ $recommendedAh }} Ah
            </p>
        </div>
    </div>

    <!-- Efficiency Score -->
    <div id="efficiencyScoreDisplay"
         class="mt-10 text-center text-lg font-semibold text-gray-700"></div>

    <!-- Appliance Ah Chart -->
    <div class="mt-10">
        <h3 class="text-lg font-semibold text-gray-700 mb-4 text-center">Ah Usage per Appliance</h3>
        <div class="relative h-96 bg-white rounded-xl shadow border p-4">
            <canvas id="powerChart"></canvas>
        </div>
    </div>

    <!-- Inverter vs Native Load -->
    <div class="mt-10">
        <h3 class="text-lg font-semibold text-gray-700 mb-4 text-center">Inverter vs Native Load</h3>
        <div class="relative h-80 w-full max-w-3xl mx-auto bg-white rounded-xl shadow border p-4">
            <canvas id="inverterNativeChart"></canvas>
        </div>
    </div>

    <!-- Chart JS -->
    @include('summary.partials.chart-scripts')

</div>
