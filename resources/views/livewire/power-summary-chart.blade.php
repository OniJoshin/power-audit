<div class="w-full max-w-4xl mx-auto">
    @if ($selectedSetupId)
        <div class="flex justify-end mt-6">
            @if ($canDownloadPdf)
                <a href="{{ route('pdf.export', $selectedSetupId) }}"
                target="_blank"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Download PDF Report
                </a>
            @else
                <p class="text-gray-500 italic">Preparing charts… please wait.</p>
            @endif
        </div>
    @endif

    {{-- Efficiency Score Display --}}
    <div wire:ignore id="efficiencyScoreDisplay" class="mt-6 text-center text-xl font-semibold text-gray-800"></div>

    {{-- Appliance-wise Ah Usage Chart --}}
    <div wire:ignore class="mt-12">
        <div class="text-lg font-semibold mb-2">Ah Usage per Appliances</div>
        <div class="relative h-96">
            <canvas id="powerChart"></canvas>
        </div>
    </div>

    {{-- Inverter vs Native Load Chart --}}
    <div wire:ignore class="mt-12">
        <div class="text-lg font-semibold mb-2">Inverter vs Native Load</div>
        <div class="relative h-80 w-full max-w-xl mx-auto">
            <canvas id="inverterNativeChart"></canvas>
        </div>
    </div>

    {{-- Chart Rendering Script --}}
        <script>
         document.addEventListener('DOMContentLoaded', function () {
            window.powerChartInstance = null;
            window.inverterChartInstance = null;

            // Per Appliance Ah Chart
            window.addEventListener('chart-data-updated', event => {
                const { data, type } = event.detail;

                const labels = data.map(item => item.name);
                const values = data.map(item => item.ah);

                const ctx = document.getElementById('powerChart').getContext('2d');

                if (window.powerChartInstance) {
                    window.powerChartInstance.destroy();
                }

                const generateColors = (count) => {
                    const palette = [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(251, 191, 36, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(244, 63, 94, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(202, 138, 4, 0.8)',
                        'rgba(6, 182, 212, 0.8)',
                        'rgba(99, 102, 241, 0.8)',
                        'rgba(129, 140, 248, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                    ];
                    return Array.from({ length: count }, (_, i) => palette[i % palette.length]);
                };

                window.powerChartInstance = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Power Usage (Ah)',
                            data: values,
                            backgroundColor: generateColors(values.length),
                            borderColor: 'rgba(0, 0, 0, 1)',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        animation: {
                            onComplete: () => {
                                setTimeout(() => {
                                    const canvas = document.getElementById('powerChart');
                                    if (canvas) {
                                        const chartImage = canvas.toDataURL('image/png');
                                        console.log('Captured chart image', chartImage);
                                        Livewire.dispatch('chartImageCaptured', { image: chartImage });
                                    }
                                }, 500);
                            }
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    generateLabels: function(chart) {
                                        const data = chart.data;
                                        const dataset = data.datasets[0];
                                        const total = dataset.data.reduce((sum, val) => sum + val, 0);

                                        return data.labels.map((label, i) => {
                                            const value = dataset.data[i];
                                            const percentage = ((value / total) * 100).toFixed(1);

                                            return {
                                                text: `${label}: ${value} Ah (${percentage}%)`,
                                                fillStyle: dataset.backgroundColor[i],
                                                strokeStyle: dataset.borderColor || '#000',
                                                lineWidth: 1,
                                                hidden: chart.getDatasetMeta(0).data[i].hidden,
                                                index: i
                                            };
                                        });
                                    }
                                }
                            }
                        }
                    }
                });
            });

            // Inverter vs Native Load Chart
            window.addEventListener('load-inverter-native-data', e => {
                const data = {
                    native: e.detail.native,
                    inverter: e.detail.inverter
                };

                const inefficiency = e.detail.inefficiency;
                const lostAh = e.detail.lostAh;



                setTimeout(() => {
                    const scoreDisplay = document.getElementById('efficiencyScoreDisplay');
                    if (scoreDisplay) {
                        scoreDisplay.textContent = `Inverter Loss: ${inefficiency}% of total Ah used (${lostAh} Ah lost)`;
                        scoreDisplay.style.color =
                            inefficiency < 5 ? 'green' :
                            inefficiency < 15 ? 'orange' : 'red';
                    }
                }, 10);


                const ctx = document.getElementById('inverterNativeChart').getContext('2d');

                if (window.inverterChartInstance) {
                    window.inverterChartInstance.destroy();
                }

                window.inverterChartInstance = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Native 12V', 'Inverter 230V'],
                        datasets: [{
                            data: [data.native, data.inverter],
                            backgroundColor: [
                                'rgba(34, 197, 94, 0.7)',   // green
                                'rgba(244, 63, 94, 0.7)'    // red
                            ],
                            borderColor: 'rgba(0, 0, 0, 1)',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        animation: {
                            onComplete: () => {
                                setTimeout(() => {
                                    const canvas = document.getElementById('inverterNativeChart');
                                    if (canvas) {
                                        const inverterImage = canvas.toDataURL('image/png');
                                        console.log('Captured inverter image', inverterImage);
                                        Livewire.dispatch('inverterImageCaptured', { image: inverterImage });
                                    }
                                }, 500);
                            }
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    generateLabels: function(chart) {
                                        const dataset = chart.data.datasets[0];
                                        const labels = chart.data.labels;
                                        const total = dataset.data.reduce((sum, val) => sum + val, 0);

                                        return labels.map((label, i) => {
                                            const value = dataset.data[i];
                                            const percentage = ((value / total) * 100).toFixed(1);

                                            return {
                                                text: `${label}: ${value.toFixed(1)} Ah (${percentage}%)`,
                                                fillStyle: dataset.backgroundColor[i],
                                                strokeStyle: dataset.borderColor || '#000',
                                                lineWidth: 1,
                                                hidden: chart.getDatasetMeta(0).data[i].hidden,
                                                index: i
                                            };
                                        });
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: ctx => `${ctx.label}: ${ctx.raw} Ah`
                                }
                            }
                        }
                    }
                });
            });
        });
    </script>
</div>
