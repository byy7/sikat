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
    public $dateStart = '';
    public $dateEnd = '';
    public $exportType = '';

    #[On('report-deleted')]
    public function refresh()
    {
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedDateStart(): void
    {
        $this->resetPage();
    }

    public function updatedDateEnd(): void
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
            'dateStart' => $this->dateStart,
            'dateEnd' => $this->dateEnd,
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
            new ReportExport($data),
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

        /* Date Filter */
        if ($this->dateStart && $this->dateEnd) {
            $query->whereBetween('created_at', [$this->dateStart, $this->dateEnd]);
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
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:justify-between">
        <div class="flex flex-col sm:flex-row items-end sm:items-end gap-2">
            <flux:field>
                <flux:label>Tanggal Awal</flux:label>
                <flux:input wire:model.live="dateStart" type="date"></flux:input>
            </flux:field>
            <flux:field>
                <flux:label>Tanggal Akhir</flux:label>
                <flux:input wire:model.live="dateEnd" type="date"></flux:input>
            </flux:field>
            <flux:field>
                <flux:label>Export Data</flux:label>
                <flux:select size="md" wire:model="exportType" placeholder="Tipe Export">
                    <option>PDF</option>
                    <option>Excel</option>
                </flux:select>
            </flux:field>
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
        <div class="w-full sm:w-auto">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass"
                        placeholder="Cari Data Pengunjung..." clearable/>
        </div>
    </div>
    <div class="hidden sm:grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-6">
        @foreach ($this->stats as $stat)
            <div
                class="rounded-lg px-6 py-4 bg-zinc-50 dark:bg-zinc-700">
                <flux:subheading>{{ $stat['title'] }}</flux:subheading>
                <flux:heading size="xl" class="mb-2">{{ $stat['value'] }}</flux:heading>
            </div>
        @endforeach
    </div>
    <div class="overflow-x-auto -mx-4 sm:mx-0">
        <div class="inline-block min-w-full align-middle">
            <div class="overflow-hidden">
                <flux:table>
                    <flux:table.columns sticky>
                        <flux:table.column>No</flux:table.column>
                        <flux:table.column>Tanggal</flux:table.column>
                        <flux:table.column>Nama</flux:table.column>
                        <flux:table.column>Keperluan</flux:table.column>
                        <flux:table.column><span>Foto</span></flux:table.column>
                        <flux:table.column>Aksi</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse($this->rows as $key => $row)
                            <flux:table.row>
                                <flux:table.cell
                                >{{ ($this->rows->currentPage() - 1) * $this->rows->perPage() + $key + 1}}</flux:table.cell>
                                <flux:table.cell>{{ $row->created_at->format('d/m/Y') }}</flux:table.cell>
                                <flux:table.cell>{{ $row->name }}</flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge :color="\App\Helpers\CustomBadgeHelper::badgeNecessary($row->necessary)"
                                                size="sm"
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
                                                <img class="mt-7 mb-3" src="{{ Storage::url($row->photo) }}"
                                                     alt="{{ $row->name }}">

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
            </div>
        </div>
    </div>
    <flux:pagination :paginator="$this->rows"/>
</div>
