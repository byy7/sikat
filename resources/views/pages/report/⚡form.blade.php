<?php

use App\Livewire\Forms\ReportForm;
use App\Models\Report;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component {
    public ReportForm $form;

    public function save()
    {
        $this->form->store();

        $this->dispatch('report-saved');

        return $this->redirect('/reports', navigate: true);
    }
};
?>

<div>
    <flux:modal.trigger name="form-store">
        <flux:button variant="primary" icon="plus-circle" class="mb-2" x-data=""
                     x-on:click.prevent="$dispatch('open-modal', 'form-store')">
            {{ __('Tambah Data') }}
        </flux:button>
    </flux:modal.trigger>

    <flux:modal name="form-store" :show="$errors->isNotEmpty()" focusable>
        <form method="POST" wire:submit="save" class="space-y-6">
            <flux:heading size="lg">{{ __('Data Pengunjung') }}</flux:heading>

            <flux:subheading>
                {{ __('Tambah/Edit Data Pengunjung') }}
            </flux:subheading>

            <flux:field>
                <flux:label badge="Wajib">Nama</flux:label>
                <flux:input type="text" autocomplete="off" wire:model="form.name" required/>
                <flux:error name="form.name"/>
            </flux:field>
            <flux:field>
                <flux:label badge="Wajib">Keperluan</flux:label>
                <flux:select wire:model="form.necessary" placeholder="Keperluan..." required>
                    @foreach(Report::NECESSARY_CHOICE as $key => $value)
                        <flux:select.option value="{{ $key }}">{{ $value }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="form.necessary"/>
            </flux:field>

            <flux:input type="file" wire:model="form.photo" label="Foto"
                        description:trailing="Foto wajib format JPG/JPEG/PNG & Maks berukuran 5 MB."/>

            <x-action-message class="mt-2 mb-2" on="report-saved">
                <flux:badge icon="check-circle" color="lime">Data berhasil tersimpan.</flux:badge>
            </x-action-message>

            <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                <flux:modal.close>
                    <flux:button variant="filled">{{ __('Batal') }}</flux:button>
                </flux:modal.close>

                <flux:button variant="primary" color="emerald" type="submit">{{ __('Simpan') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
