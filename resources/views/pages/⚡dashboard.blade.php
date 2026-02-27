<?php

use App\Models\Report;
use App\Consts\Month;
use Livewire\Component;

new class extends Component {
    public $month = '';
    public $year = '';
    public array $visitorData = [];

    public function mount(): void
    {
        $this->refresh();
    }

    public function updatedMonth(): void
    {
        $this->refresh();
    }

    public function updatedYear(): void
    {
        $this->refresh();
    }

    public function resetFilters(): void
    {
        $this->month = '';
        $this->year = '';
        $this->refresh();
    }

    private function refresh(): void
    {
        $query = $this->getFilteredQuery();

        $this->visitorData = [
            [
                'label' => 'Total pihak perkara',
                'count' => (clone $query)->where('necessary', 'pihak_perkara')->count(),
            ],
            [
                'label' => 'Total saksi',
                'count' => (clone $query)->where('necessary', 'saksi')->count(),
            ],
            [
                'label' => 'Total tamu',
                'count' => (clone $query)->where('necessary', 'tamu')->count(),
            ],
            [
                'label' => 'Total kuasa hukum',
                'count' => (clone $query)->where('necessary', 'kuasa_hukum')->count(),
            ],
            [
                'label' => 'Total pengunjung',
                'count' => (clone $query)->get(['id'])->count(),
            ],
        ];

        $this->dispatch('update-chart', data: $this->visitorData);
    }

    private function getFilteredQuery()
    {
        $query = Report::query();

        /* Month Filter */
        if ($this->month) {
            $monthNumber = Month::getFullMonth()->search($this->month) + 1;
            $query->whereMonth('created_at', $monthNumber);
        }

        /* Year Filter */
        if ($this->year) {
            $query->whereYear('created_at', $this->year);
        }

        return $query;
    }
};
?>

<div class="relative w-full">
    <flux:card size="md" class="hover:bg-zinc-50 dark:hover:bg-zinc-700">
        <flux:heading class="flex items-center gap-2 mb-4">Filter Bulan & Tahun</flux:heading>

        <!-- Responsive Filter Section -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 mb-4">
            <flux:select wire:model.live="month" size="md" placeholder="Bulan" class="w-full sm:flex-1">
                @foreach(Month::getFullMonth() as $month)
                    <flux:select.option>{{ $month }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:select wire:model.live="year" size="md" placeholder="Tahun" class="w-full sm:flex-1">
                @foreach(Report::getYears() as $year)
                    <flux:select.option value="{{ $year }}">{{ $year }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:tooltip content="Reset Filter">
                <flux:button wire:click="resetFilters" icon="x-mark" class="w-full sm:w-auto"/>
            </flux:tooltip>
        </div>

        <flux:separator variant="subtle"/>

        <!-- Title -->
        <div class="mt-5 mb-5 text-center">
            <flux:heading size="lg">Data Pengunjung</flux:heading>
        </div>

        <!-- Responsive Chart and Table Layout -->
        <div class="flex flex-col lg:flex-row justify-center items-start lg:items-center gap-6">
            <!-- Data Table -->
            <div class="w-full lg:w-auto">
                <table class="w-full lg:w-auto">
                    <tbody>
                    @foreach($this->visitorData as $item)
                        <tr class="border-b border-zinc-200 dark:border-zinc-600 last:border-0">
                            <th class="py-2 pr-4 text-left font-medium text-sm whitespace-nowrap">{{ $item['label'] }}</th>
                            <td class="py-2 px-2 text-center">:</td>
                            <td class="py-2 pl-4 font-semibold text-sm">{{ $item['count'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Chart -->
            <div class="w-full max-w-sm lg:max-w-md mx-auto">
                <div class="aspect-square w-full">
                    <canvas id="visitorChart" wire:ignore></canvas>
                </div>
            </div>
        </div>
    </flux:card>
</div>

@assets
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endassets

@script
<script>
    let chartInstance = null;

    function initVisitorChart(data = null) {
        if (typeof Chart === 'undefined') {
            console.error('Chart.js not loaded');
            return;
        }

        const canvas = document.getElementById('visitorChart');
        if (!canvas) {
            console.error('Canvas element not found');
            return;
        }

        // Destroy existing chart instance if it exists
        if (chartInstance) {
            chartInstance.destroy();
            chartInstance = null;
        }

        let source = data || @json($visitorData);

        if (source && typeof source === 'object') {
            // If it's an object with a visitorData property
            if (source.visitorData) {
                source = source.visitorData;
            }
            // If it's an object but not an array, try to convert to array
            else if (!Array.isArray(source) && source.length === undefined) {
                source = Object.values(source);
            }
        }

        if (!source || !Array.isArray(source) || source.length === 0) {
            console.warn('No valid data available for chart');
            return;
        }

        const labels = source.map(item => item.label);
        const counts = source.map(item => item.count);
        const colors = ['#6BCF9D', '#4D96FF', '#FF6B6B', '#FFC75F', '#de7c09'];

        const customLabels = {
            id: 'customLabels',
            afterDatasetDraw(chart) {
                const {ctx} = chart;
                const meta = chart.getDatasetMeta(0);

                // Calculate total only from visible segments
                const visibleData = meta.data
                    .map((element, index) => element.hidden ? 0 : chart.data.datasets[0].data[index])
                    .filter(val => val > 0);
                const total = visibleData.reduce((a, b) => a + b, 0);

                if (total === 0) return;

                meta.data.forEach((element, index) => {
                    if (element.hidden) return;

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

        chartInstance = new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Data',
                    data: counts,
                    backgroundColor: colors,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        labels: {
                            color: getComputedStyle(document.documentElement)
                                .getPropertyValue('color') || '#374151',
                            font: {
                                size: 11
                            },
                            padding: 15
                        },
                        display: true,
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            },
            plugins: [customLabels]
        });
    }

    /* Initial Render */
    initVisitorChart();

    /* Listen for filter changes */
    Livewire.on('update-chart', (event) => {
        // Handle the event data - it could be wrapped in different ways
        let data = event.data;

        initVisitorChart(data);
    });
</script>
@endscript
