<div class="w-full max-w-4xl mx-auto">

    {{-- Chart Type Selector with entangle --}}
    <div x-data="{ chartType: @entangle('chartType').defer }" class="flex justify-end mb-4">
        <label class="mr-2 text-sm">Chart Type:</label>
        <select
            x-model="chartType"
            @change="setTimeout(() => $wire.dispatchChartData(), 10)"

            class="border rounded px-2 py-1 text-sm text-gray-700"
        >
            <option value="bar">Bar</option>
            <option value="line">Line</option>
            <option value="doughnut">Doughnut</option>
            <option value="pie">Pie</option>
        </select>
    </div>
    

    {{-- Canvas Chart Area --}}
    <div wire:ignore>
        <div class="relative h-96">
            <canvas id="powerChart"></canvas>
        </div>
    </div>

    {{-- Chart Rendering Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.powerChartInstance = null;

            window.addEventListener('chart-data-updated', event => {
                const { data, type } = event.detail;
                const chartType = type || 'bar';

                const labels = data.map(item => item.name);
                const values = data.map(item => item.wh);

                const ctx = document.getElementById('powerChart').getContext('2d');

                if (window.powerChartInstance) {
                    window.powerChartInstance.destroy();
                }

                window.powerChartInstance = new Chart(ctx, {
                    type: chartType,
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Power Usage (Wh)',
                            data: values,
                            backgroundColor: 'rgba(59, 130, 246, 0.5)',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Wh/day'
                                }
                            }
                        },
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            });
        });
    </script>
</div>
