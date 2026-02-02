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

<div>
    <div class="relative w-full">
        <flux:card size="md" class="hover:bg-zinc-50 dark:hover:bg-zinc-700">
            <flux:heading class="flex items-center gap-2 mb-4">Filter Bulan & Tahun</flux:heading>
            <div class="flex item-center gap-2 mb-2">
                <flux:select wire:model.live="month" size="md" placeholder="Bulan">
                    @foreach(Month::getFullMonth() as $month)
                        <flux:select.option>{{ $month }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:select wire:model.live="year" size="md" placeholder="Tahun">
                    @foreach(Report::getYears() as $year)
                        <flux:select.option value="{{ $year }}">{{ $year }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:tooltip content="Reset Filter">
                    <flux:button wire:click="resetFilters" icon="x-mark"/>
                </flux:tooltip>
            </div>
            <flux:separator variant="subtle"/>
            <div class="mt-5 mb-5 text-center">
                Data Pengunjung
            </div>
            <div class="flex justify-center gap-4">
                <div class="text-left">
                    <table>
                        @foreach($this->visitorData as $item)
                            <tr>
                                <th>{{ $item['label'] }}</th>
                                <td>&nbsp;:&nbsp;</td>
                                <td>{{ $item['count'] }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                <div class="w-full max-w-sm h-64 mx-auto" wire:ignore>
                    <canvas id="visitorChart"></canvas>
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
            if (typeof Chart === 'undefined') return;

            const canvas = document.getElementById('visitorChart');

            if (!canvas) return;

            // Destroy existing chart instance if it exists
            if (chartInstance) {
                chartInstance.destroy();
                chartInstance = null;
            }

            const source = data || @json($visitorData);

            const labels = source.map(item => item.label);
            const counts = source.map(item => item.count);
            const colors = ['#FF6B6B', '#4D96FF', '#FFC75F', '#6BCF9D'];

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
                options:
                    {
                        responsive: true,
                        maintainAspectRatio:
                            true,
                        plugins:
                            {
                                legend: {
                                    labels: {
                                        color: '#ffffff', // White color
                                        font:
                                            {
                                                size: 9
                                            }
                                    }
                                    ,
                                    display: true,
                                    position:
                                        'top'
                                }
                                ,
                            }
                    },
                plugins: [customLabels]
            });
        }

        /* Initial Render */
        initVisitorChart();

        /* Listen filter change */
        document.addEventListener('livewire:init', () => {
            Livewire.on('update-chart', (data) => {
                initVisitorChart(data);
            });
        });
    </script>
    @endscript
</div>
