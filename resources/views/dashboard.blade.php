@php use App\Consts\Month; @endphp

<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <flux:card size="md" class="hover:bg-zinc-50 dark:hover:bg-zinc-700 text-center">
            <flux:heading class="flex items-center gap-2 mb-4">Filter Bulan & Tahun</flux:heading>
            <div class="grid auto-rows-min gap-4 md:grid-cols-2">
                <flux:select wire:model="industry" placeholder="Bulan">
                    <flux:select.option>Semua</flux:select.option>
                    @foreach(Month::getFullMonth() as $month)
                        <flux:select.option>{{ $month }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:select wire:model="industry" placeholder="Tahun">
                    <flux:select.option>Semua</flux:select.option>
                </flux:select>
            </div>
        </flux:card>

        <div class="grid grid-cols-12 gap-4">
            <flux:card size="md" class="hover:bg-zinc-50 dark:hover:bg-zinc-700 col-span-4">
                <div class="mb-7 text-center">
                    Data Pengunjung
                </div>
                <div class="text-left">
                    <table>
                        <tr>
                            <th>Pihak Perkara</th>
                            <td>&nbsp:&nbsp</td>
                            <td>90</td>
                        </tr>
                        <tr>
                            <th>Saksi</th>
                            <td>&nbsp:&nbsp</td>
                            <td>70</td>
                        </tr>
                        <tr>
                            <th>Tamu</th>
                            <td>&nbsp:&nbsp</td>
                            <td>60</td>
                        </tr>
                        <tr>
                            <th>Kuasa Hukum</th>
                            <td>&nbsp:&nbsp</td>
                            <td>50</td>
                        </tr>
                    </table>
                </div>
            </flux:card>
            <flux:card size="md" class="hover:bg-zinc-50 dark:hover:bg-zinc-700 text-center col-span-8">
                <div class="text-center max-w-sm max-h-sm mx-auto">
                    <canvas id="visitorChart"></canvas>
                </div>
            </flux:card>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function initVisitorChart() {
            if (typeof Chart === 'undefined') {
                return;
            }

            const canvas = document.getElementById('visitorChart');

            if (!canvas) return;

            // Destroy existing chart instance if it exists
            const existingChart = Chart.getChart(canvas);
            if (existingChart) {
                existingChart.destroy();
            }

            const customLabels = {
                id: 'customLabels',
                afterDatasetDraw(chart) {
                    const {ctx, chartArea: {width, height}} = chart;
                    const meta = chart.getDatasetMeta(0);

                    // Calculate total only from visible segments
                    const visibleData = meta.data
                        .map((element, index) => element.hidden ? 0 : chart.data.datasets[0].data[index])
                        .filter(val => val > 0);
                    const total = visibleData.reduce((a, b) => a + b, 0);

                    meta.data.forEach((element, index) => {
                        // Skip if hidden
                        if (element.hidden) {
                            return;
                        }

                        const {x, y} = element.tooltipPosition();
                        const value = chart.data.datasets[0].data[index];
                        const percentage = ((value / total) * 100).toFixed(1) + '%';

                        ctx.fillStyle = '#fff';
                        ctx.font = 'bold 14px Arial';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillText(percentage, x, y);
                    });
                }
            };

            const data = {
                labels: ['Pihak Perkara', 'Saksi', 'Tamu', 'Kuasa Hukum'],
                datasets: [{
                    label: 'Jumlah Data',
                    data: [300, 50, 100, 250],
                    backgroundColor: [
                        '#FF6B6B',
                        '#4D96FF',
                        '#FFC75F',
                        '#6BCF9D',
                    ],
                    hoverOffset: 4
                }]
            };

            const options = {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        labels: {
                            color: '#ffffff', // White color
                            font: {
                                size: 9
                            }
                        },
                        display: true,
                        position: 'top'
                    },
                }
            };

            new Chart(canvas, {
                type: 'doughnut',
                data: data,
                options: options,
                plugins: [customLabels]
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initVisitorChart);
        } else {
            initVisitorChart();
        }

        document.addEventListener('livewire:navigated', initVisitorChart);
    </script>
</x-layouts::app>
