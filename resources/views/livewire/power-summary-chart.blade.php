<div wire:ignore.self class="mt-8 max-w-4xl mx-auto bg-white shadow rounded p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-800">Power Usage Summary</h3>

        {{-- Chart type selector --}}
        <select wire:model="chartType" wire:change="loadChartData('{{ $selectedSetupId }}')" class="border rounded px-2 py-1 text-sm text-gray-700">
            <option value="bar">Bar</option>
            <option value="line">Line</option>
            <option value="doughnut">Doughnut</option>
            <option value="pie">Pie</option>
        </select>
    </div>

    @if (count($applianceData))

        <div wire:ignore.self x-data data-chart-type="{{ $chartType }}" class="...">
            <div class="relative w-full h-96">
                <canvas id="powerChart" class="absolute inset-0 w-full h-full"></canvas>
            </div>

            <script>
    document.addEventListener('DOMContentLoaded', function () {
        window.addEventListener('chart-data-updated', event => {
            const { data, type } = event.detail;
            renderChart(data, type || 'bar');
        });
    });

    function renderChart(data, type = 'bar') {
        const canvas = document.getElementById('powerChart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        if (window.powerChartInstance) {
            window.powerChartInstance.destroy();
        }

        const labels = data.map(item => item.name);
        const values = data.map(item => item.wh);

        window.powerChartInstance = new Chart(ctx, {
            type,
            data: {
                labels,
                datasets: [{
                    label: 'Power Usage (Wh)',
                    data: values,
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.6)',
                        'rgba(251, 191, 36, 0.6)',
                        'rgba(34, 197, 94, 0.6)',
                        'rgba(244, 63, 94, 0.6)'
                    ],
                    borderColor: 'rgba(0,0,0,0.1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: type !== 'bar'
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => `${ctx.raw} Wh`
                        }
                    }
                },
                scales: type === 'bar' || type === 'line'
                    ? {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Wh/day'
                            }
                        }
                    }
                    : {}
            }
        });
    }
</script>

        </div>
    @else
        <p class="text-gray-500 italic">Select a setup to view power usage breakdown.</p>
    @endif
</div>
