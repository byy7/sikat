<?php

use App\Exports\ReportExport;
use App\Models\Report;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use LaravelIdea\Helper\App\Models\_IH_Report_C;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use App\Consts\Month;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $month = '';
    public $year = '';
    public $exportType = '';

    #[On('report-deleted')]
    public function refresh()
    {
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedMonth(): void
    {
        $this->resetPage();
    }

    public function updatedYear(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function stats(): array
    {
        $query = $this->getFilteredQuery();

        return [
            [
                'title' => 'Total pihak perkara',
                'value' => (clone $query)->where('necessary', 'pihak_perkara')->count(),
            ],
            [
                'title' => 'Total saksi',
                'value' => (clone $query)->where('necessary', 'saksi')->count(),
            ],
            [
                'title' => 'Total tamu',
                'value' => (clone $query)->where('necessary', 'tamu')->count(),
            ],
            [
                'title' => 'Total kuasa hukum',
                'value' => (clone $query)->where('necessary', 'kuasa_hukum')->count(),
            ]
        ];
    }

    #[Computed]
    public function rows(): array|LengthAwarePaginator|_IH_Report_C
    {
        return $this->getFilteredQuery()
            ->latest()
            ->paginate(10);
    }

    public function export()
    {
        if (!$this->exportType) {
            $this->dispatch('error-export');
            return;
        }

        $data = $this->getFilteredQuery()->latest()->get();

        if ($this->exportType === 'PDF') {
            return $this->exportPdf($data);
        } elseif ($this->exportType === 'Excel') {
            return $this->exportExcel($data);
        }
    }

    public function resetFilters(): void
    {
        $this->reset();
        $this->resetPage();
    }

    private function exportPdf($data): StreamedResponse
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pages.report.export', [
            'reports' => $data,
            'month' => $this->month,
            'year' => $this->year,
            'stats' => $this->stats
        ]);

        $filename = 'Laporan Pengunjung-' . now()->format('Y-m-d-His') . '.pdf';

        $this->reset();
        $this->resetPage();

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }

    private function exportExcel($data): BinaryFileResponse
    {
        $this->reset();
        $this->resetPage();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new ReportExport($data, $this->month, $this->year),
            'Laporan Pengunjung-' . now()->format('Y-m-d-His') . '.xlsx'
        );
    }

    private function getFilteredQuery()
    {
        $query = Report::query();

        /* Search Filter */
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%$this->search%")
                    ->orWhere('necessary', 'like', "%$this->search%");
            });
        }

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
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Data Pengunjung') }}</flux:heading>
        <flux:subheading size="lg" class="mb-4">{{ __('Kelola laporan & data pengunjung') }}</flux:subheading>
        <livewire:pages::report.forms.create/>
        <flux:separator variant="subtle"/>
    </div>
    <div class="mb-6 flex justify-between">
        <div class="flex items-center gap-2">
            <flux:select wire:model.live="month" size="md" placeholder="Bulan">
                @foreach(Month::getFullMonth() as $month)
                    <flux:select.option value="{{ $month }}">{{ $month }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:select wire:model.live="year" size="md" placeholder="Tahun">
                @foreach(Report::getYears() as $year)
                    <flux:select.option value="{{ $year }}">{{ $year }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:select size="md" wire:model="exportType" placeholder="Export">
                <option>PDF</option>
                <option>Excel</option>
            </flux:select>
            <flux:tooltip content="Reset Filter">
                <flux:button wire:click="resetFilters" icon="x-mark"/>
            </flux:tooltip>
            <flux:tooltip content="Export Data">
                <flux:button wire:click="export" icon="arrow-down-on-square-stack" icon:variant="outline"/>
            </flux:tooltip>
            <flux:tooltip content="Refresh">
                <flux:button wire:click="$refresh" icon="arrow-path"/>
            </flux:tooltip>
            <x-action-message on="error-export">
                <flux:badge icon="check-circle" color="rose">Silahkan isi filter terlebih dahulu!</flux:badge>
            </x-action-message>
        </div>
        <div class="items-center gap-2">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass"
                        placeholder="Cari Data Pengunjung..." clearable/>
        </div>
    </div>
    <div class="flex gap-6 mb-6">
        @foreach ($this->stats as $stat)
            <div
                class="relative flex-1 rounded-lg px-6 py-4 bg-zinc-50 dark:bg-zinc-700">
                <flux:subheading>{{ $stat['title'] }}</flux:subheading>
                <flux:heading size="xl" class="mb-2">{{ $stat['value'] }}</flux:heading>
            </div>
        @endforeach
    </div>
    <flux:table>
        <flux:table.columns sticky>
            <flux:table.column class="max-md:hidden">No</flux:table.column>
            <flux:table.column class="max-md:hidden">Nama</flux:table.column>
            <flux:table.column class="max-md:hidden">Keperluan</flux:table.column>
            <flux:table.column><span class="max-md:hidden">Foto</span>
                <div class="md:hidden w-6"></div>
            </flux:table.column>
            <flux:table.column>Aksi</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse($this->rows as $key => $row)
                <flux:table.row>
                    <flux:table.cell
                        class="max-md:hidden">{{ ($this->rows->currentPage() - 1) * $this->rows->perPage() + $key + 1}}</flux:table.cell>
                    <flux:table.cell class="max-md:hidden">{{ $row->name }}</flux:table.cell>
                    <flux:table.cell class="max-md:hidden">
                        <flux:badge :color="\App\Helpers\CustomBadgeHelper::badgeNecessary($row->necessary)" size="sm"
                                    inset="top bottom">{{ $row->necessary }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell class="min-w-6">
                        <div class="flex items-center gap-2">
                            @if(!is_null($row->photo))
                                <flux:modal.trigger :name="'show-photo-' . $row->id">
                                    <flux:avatar as="button" src="{{ Storage::url($row->photo) }}"/>
                                </flux:modal.trigger>

                                {{-- Modal --}}
                                <flux:modal :name="'show-photo-' . $row->id">
                                    <img class="mt-7 mb-3" src="{{ Storage::url($row->photo) }}" alt="{{ $row->name }}">

                                    <div class="flex justify-start space-x-2 rtl:space-x-reverse">
                                        <flux:modal.close>
                                            <flux:button variant="filled">{{ __('Tutup') }}</flux:button>
                                        </flux:modal.close>
                                        <div x-data="{ downloading: false }">
                                            <flux:button
                                                href="{{ route('reports.download', encrypt($row->id)) }}"
                                                @click="downloading = true; setTimeout(() => downloading = false, 2000)"
                                                icon="arrow-down-tray"
                                                variant="primary"
                                                ::disabled="downloading">
                                                <span x-show="!downloading">{{ __('Download') }}</span>
                                                <span x-show="downloading">{{ __('Downloading...') }}</span>
                                            </flux:button>
                                        </div>
                                    </div>
                                </flux:modal>
                            @else
                                <p>Foto tidak ditemukan</p>
                            @endif
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:dropdown position="bottom" align="end" offset="-15">
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal"
                                         inset="top bottom"></flux:button>
                            <flux:menu>
                                <flux:modal.trigger :name="'edit-report-' . $row->id">
                                    <flux:menu.item icon="pencil-square">Edit</flux:menu.item>
                                </flux:modal.trigger>
                                <flux:modal.trigger :name="'delete-report-' . $row->id">
                                    <flux:menu.item icon="trash" variant="danger">Hapus</flux:menu.item>
                                </flux:modal.trigger>
                            </flux:menu>
                        </flux:dropdown>
                        <livewire:pages::report.forms.edit :reportId="$row->id"/>
                        <livewire:pages::report.forms.delete :reportId="$row->id"/>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row class="text-center">
                    <flux:table.cell colspan="5">Data Tidak Tersedia</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
    <flux:pagination :paginator="$this->rows"/>
</div>
