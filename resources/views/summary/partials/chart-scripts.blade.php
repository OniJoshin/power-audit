@push('scripts')
<script>
    let chartUploadStatus = {
        power: false,
        inverter: false
    };

    function uploadChartImage(name, imageData) {
        console.log(`Uploading ${name} chart...`);
        fetch('{{ route('charts.upload') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ name, image: imageData })
        }).then(response => {
            if (response.ok) {
                console.log(`${name} chart uploaded successfully.`);
                chartUploadStatus[name] = true;
                checkIfReadyToEnablePDF();
            } else {
                console.error(`${name} upload failed.`);
            }
        }).catch(error => {
            console.error(`${name} upload error:`, error);
        });
    }


    function checkIfReadyToEnablePDF() {
        if (chartUploadStatus.power && chartUploadStatus.inverter) {
            console.log('Both charts uploaded. Showing PDF button.');
            const msg = document.getElementById('pdfStatus');
            const btn = document.getElementById('pdfButton');
            if (msg) msg.remove();
            if (btn) btn.classList.remove('hidden');
        }
    }

    window.powerChartInstance = null;
    window.inverterChartInstance = null;

    document.addEventListener('DOMContentLoaded', () => {
        const applianceData = @json($applianceAhData);
        const inverterData = {
            native: {{ $nativeAh }},
            inverter: {{ $inverterAh }},
            inefficiency: {{ $inefficiency }},
            lostAh: {{ $lostAh }}
        };

        renderPowerChart(applianceData);
        renderInverterChart(inverterData);
    });

    function renderPowerChart(data) {
        const labels = data.map(item => item.name);
        const values = data.map(item => item.ah);
        const ctx = document.getElementById('powerChart')?.getContext('2d');
        if (!ctx) return;

        if (window.powerChartInstance) window.powerChartInstance.destroy();

        const generateColors = (count) => {
            const palette = [
                'rgba(59, 130, 246, 0.8)', 'rgba(251, 191, 36, 0.8)', 'rgba(34, 197, 94, 0.8)',
                'rgba(244, 63, 94, 0.8)', 'rgba(168, 85, 247, 0.8)', 'rgba(16, 185, 129, 0.8)',
                'rgba(239, 68, 68, 0.8)', 'rgba(236, 72, 153, 0.8)', 'rgba(202, 138, 4, 0.8)',
                'rgba(6, 182, 212, 0.8)', 'rgba(99, 102, 241, 0.8)', 'rgba(129, 140, 248, 0.8)',
                'rgba(245, 158, 11, 0.8)'
            ];
            return Array.from({ length: count }, (_, i) => palette[i % palette.length]);
        };

        window.powerChartInstance = new Chart(ctx, {
            type: 'pie',
            data: {
                labels,
                datasets: [{
                    label: 'Power Usage (Ah)',
                    data: values,
                    backgroundColor: generateColors(values.length),
                    borderColor: 'rgba(0, 0, 0, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    onComplete: () => {
                        setTimeout(() => {
                            const canvas = document.getElementById('powerChart');
                            if (canvas) {
                                const imageData = canvas.toDataURL('image/png');
                                uploadChartImage('power', imageData);
                            }
                        }, 300);
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            generateLabels: function(chart) {
                                const dataset = chart.data.datasets[0];
                                const total = dataset.data.reduce((sum, val) => sum + val, 0);
                                return chart.data.labels.map((label, i) => {
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
    }

    function renderInverterChart({ native, inverter, inefficiency, lostAh }) {
        const ctx = document.getElementById('inverterNativeChart')?.getContext('2d');
        if (!ctx) return;

        if (window.inverterChartInstance) window.inverterChartInstance.destroy();

        const scoreDisplay = document.getElementById('efficiencyScoreDisplay');
        if (scoreDisplay) {
            scoreDisplay.textContent = `Inverter Loss: ${inefficiency}% of total Ah used (${lostAh} Ah lost)`;
            scoreDisplay.style.color =
                inefficiency < 5 ? 'green' :
                inefficiency < 15 ? 'orange' : 'red';
        }

        window.inverterChartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Native 12V', 'Inverter 230V'],
                datasets: [{
                    data: [native, inverter],
                    backgroundColor: ['rgba(34, 197, 94, 0.7)', 'rgba(244, 63, 94, 0.7)'],
                    borderColor: 'rgba(0, 0, 0, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    onComplete: () => {
                        setTimeout(() => {
                            const canvas = document.getElementById('inverterNativeChart');
                            if (canvas) {
                                const imageData = canvas.toDataURL('image/png');
                                uploadChartImage('inverter', imageData);
                            }
                        }, 300);
                    }
                },
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
                    }
                }
            }
        });
    }
</script>
@endpush
