<?php

use App\Models\Report;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use App\Consts\Month;

new class extends Component {
    use WithPagination;

    #[On('report-deleted')]
    public function refresh(){}

    #[Computed]
    public function stats(): array
    {
        return [
            [
                'title' => 'Total pihak perkara',
                'value' => Report::GetTotalData("pihak_perkara"),
            ],
            [
                'title' => 'Total saksi',
                'value' => Report::GetTotalData("saksi"),
            ],
            [
                'title' => 'Total tamu',
                'value' => Report::GetTotalData("tamu"),
            ],
            [
                'title' => 'Total kuasa hukum',
                'value' => Report::GetTotalData("kuasa_hukum"),
            ]
        ];
    }

    #[Computed]
    public function rows()
    {
        return Report::latest()
            ->paginate(10);
    }
};
?>

<div class="relative w-full">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Data Pengunjung') }}</flux:heading>
        <flux:subheading size="lg" class="mb-4">{{ __('Kelola laporan & data pengunjung') }}</flux:subheading>
        <livewire:pages::report.form/>
        <x-action-message class="mt-2 mb-2" on="report-deleted">
            <flux:badge icon="check-circle" color="rose">Data berhasil terhapus.</flux:badge>
        </x-action-message>
        <flux:separator variant="subtle"/>
    </div>
    <div class="mb-6 flex justify-between">
        <div class="flex items-center gap-2">
            <flux:select size="md" placeholder="Bulan">
                <flux:select.option>Semua</flux:select.option>
                @foreach(Month::getFullMonth() as $month)
                    <flux:select.option>{{ $month }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:select size="md" placeholder="Tahun">
                <option>Previous period</option>
                <option>Same period last year</option>
                <option>Last month</option>
                <option>Last quarter</option>
                <option>Last 6 months</option>
                <option>Last 12 months</option>
            </flux:select>
            <flux:select size="md" placeholder="Export">
                <option>PDF</option>
                <option>Excel</option>
            </flux:select>
            <flux:tooltip content="Export Data">
                <flux:button icon="arrow-down-on-square-stack" icon:variant="outline"/>
            </flux:tooltip>
            <flux:tooltip content="Refresh">
                <flux:button wire:click="$refresh" icon="arrow-path"/>
            </flux:tooltip>
        </div>
        <div class="items-center gap-2">
            <flux:input icon="magnifying-glass" placeholder="Cari Data Pengunjung..." clearable/>
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
                @php
                    $encryptedId = encrypt($row->id);
                @endphp
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
                            @if(!is_null($row->photo) && $row->photo !== "")
                                <flux:avatar href="{{ Storage::url($row->photo) }}" target="_blank"
                                             src="{{ Storage::url($row->photo) }}" size="xl"/>
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
                                <flux:menu.item icon="pencil-square">Edit</flux:menu.item>
                                <flux:modal.trigger :name="'delete-report-' . $encryptedId">
                                    <flux:menu.item icon="trash" variant="danger">Hapus</flux:menu.item>
                                </flux:modal.trigger>
                            </flux:menu>
                        </flux:dropdown>
                        <livewire:pages::report.forms.delete :reportId="$encryptedId"/>
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
