<div wire:ignore.self>
    @if (count($applianceData))
        <div class="relative w-full max-w-3xl h-72">
            <canvas id="powerChart" class="absolute top-0 left-0 w-full h-full"></canvas>
        </div>

        <script>
    document.addEventListener("DOMContentLoaded", () => {
        Livewire.hook('message.processed', (message, component) => {
            if (component.fingerprint.name !== 'power-summary-chart') return;

            // Delay render until canvas is in DOM
            waitForCanvasThenRender(@json($applianceData));
        });
    });

    function waitForCanvasThenRender(data) {
        let attempts = 0;

        const tryRender = () => {
            const canvas = document.getElementById('powerChart');
            if (!canvas || attempts > 10) return;

            const ctx = canvas.getContext('2d');
            if (!ctx) {
                attempts++;
                return setTimeout(tryRender, 100); // retry
            }

            if (window.powerChartInstance) {
                window.powerChartInstance.destroy();
            }

            const labels = data.map(item => item.name);
            const values = data.map(item => item.wh);

            window.powerChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
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
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Wh/day'
                            }
                        }
                    }
                }
            });
        };

        tryRender();
    }
</script>

    @else
        <p class="text-gray-500 italic">Select a setup to view power usage breakdown.</p>
    @endif
</div>
