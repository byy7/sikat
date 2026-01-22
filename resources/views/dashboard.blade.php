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

        <flux:card size="md" class="hover:bg-zinc-50 dark:hover:bg-zinc-700 text-center">
            <div class="text-lg mb-1">
                Data Pengunjung & Pihak Perkara
            </div>
            <div class="text-center max-w-sm max-h-sm mx-auto">
                <canvas id="visitorChart"></canvas>
            </div>
        </flux:card>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('visitorChart');

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
            labels: ['Pengunjung', 'Saksi', 'Tamu', 'Kuasa Hukum'],
            datasets: [{
                label: 'Jumlah Data',
                data: [300, 50, 100, 250],
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 205, 86)',
                    'rgb(190, 205, 86)',
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

        new Chart(ctx, {
            type: 'doughnut',
            data: data,
            options: options,
            plugins: [customLabels]
        });
    </script>
</x-layouts::app>
