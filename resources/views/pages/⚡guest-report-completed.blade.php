<?php

use App\Models\Report;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::auth')]
class extends Component {
    public ?Report $report;
    public string $name;
    public $necessary;

    public function mount(string $id): void
    {
        $this->report = Report::find(decrypt($id));
        $this->name = $this->report->name;
        $this->necessary = $this->report->necessary;
    }
};
?>

<div>
    <div class="absolute inset-0 -z-10 bg-cover bg-center bg-no-repeat"
         style="background-image: url('{{ asset('assets/img/gedung1.webp') }}');opacity: 35%"></div>

    <div class="space-y-6">
        <flux:heading size="lg">{{ __('Data Pengunjung') }}</flux:heading>

        <div x-data="{ visible: true }" x-show="visible" x-collapse>
            <div x-show="visible" x-transition>
                <flux:callout icon="information-circle" variant="secondary" inline x-data="{ visible: true }" x-show="visible">
                    <flux:callout.heading class="flex gap-2 @max-md:flex-col items-start">Data berhasil disimpan!
                        <flux:text>Silahkan tutup halaman ini jika sudah selesai mengisi data.</flux:text>
                    </flux:callout.heading>

                    <x-slot name="controls">
                        <flux:button icon="x-mark" variant="ghost" x-on:click="visible = false"/>
                    </x-slot>
                </flux:callout>
            </div>
        </div>

        <flux:field>
            <flux:label>Nama</flux:label>
            <flux:input type="text" autocomplete="off" wire:model="name" disabled required/>
            <flux:error name="name"/>
        </flux:field>
        <flux:field>
            <flux:label>Keperluan</flux:label>
            <flux:select wire:model="necessary" placeholder="Keperluan..." disabled required>
                @foreach(Report::NECESSARY_CHOICE as $key => $value)
                    <flux:select.option value="{{ $key }}">{{ $value }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:error name="necessary"/>
        </flux:field>

        @if($this->report->photo)
            <flux:card>
                <div>
                    <flux:heading size="lg">Foto Dokumentasi</flux:heading>
                </div>

                <div class="space-y-6">
                    <img width="150" src="{{ \Illuminate\Support\Facades\Storage::url($this->report->photo) }}"
                         alt="Photo">
                </div>
            </flux:card>
        @endif
    </div>
</div>
