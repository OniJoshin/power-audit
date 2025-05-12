<div>
    @if (count($applianceData))
        <canvas id="powerChart" wire:ignore></canvas>

        <script>
            document.addEventListener('livewire:load', () => {
                Livewire.hook('message.processed', (message, component) => {
                    if (component.fingerprint.name !== 'power-summary-chart') return;

                    const chartCanvas = document.getElementById('powerChart');
                    const ctx = chartCanvas.getContext('2d');
                    if (window.powerChartInstance) window.powerChartInstance.destroy();

                    const data = @json($applianceData);
                    const labels = data.map(a => a.name);
                    const values = data.map(a => a.wh);

                    window.powerChartInstance = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels,
                            datasets: [{
                                label: 'Daily Power Use (Wh)',
                                data: values,
                                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                                borderColor: 'rgba(59, 130, 246, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: ctx => `${ctx.raw} Wh`
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: { display: true, text: 'Wh/day' }
                                }
                            }
                        }
                    });
                });
            });
        </script>
    @else
        <p class="text-gray-500 italic">Select a setup to view power usage breakdown.</p>
    @endif
</div>
