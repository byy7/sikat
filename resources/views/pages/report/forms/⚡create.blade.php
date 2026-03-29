<?php

use App\Livewire\Forms\ReportForm;
use App\Models\Report;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public ReportForm $form;

    public function resetForm(): void
    {
        $this->form->reset();
        $this->form->resetValidation();
    }

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
                     wire:click="resetForm">
            {{ __('Tambah Data') }}
        </flux:button>
    </flux:modal.trigger>

    <flux:modal name="form-store" :show="$errors->isNotEmpty()"
                :dismissible="false">
        <form method="POST" wire:submit="save" class="space-y-6">
            <flux:heading size="lg">{{ __('Data Pengunjung') }}</flux:heading>

            <flux:subheading>
                {{ __('Tambah Data Pengunjung') }}
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

            <div
                x-data="{ uploading: false, progress: 0 }"
                x-on:livewire-upload-start="uploading = true"
                x-on:livewire-upload-finish="uploading = false"
                x-on:livewire-upload-cancel="uploading = false"
                x-on:livewire-upload-error="uploading = false"
                x-on:livewire-upload-progress="progress = $event.detail.progress">

                <flux:input type="file" wire:model="form.photo" label="Foto" accept="image/*"
                            description:trailing="Foto wajib format JPG/JPEG/PNG." required/>

                <div wire:loading wire:target="form.photo">Uploading...</div>

                <!-- Progress Bar -->
                <div x-show="uploading">
                    <progress max="100" x-bind:value="progress"></progress>
                </div>
            </div>
            <x-action-message class="mt-2 mb-2" on="report-saved">
                <flux:badge icon="check-circle" color="lime">Data berhasil tersimpan.</flux:badge>
            </x-action-message>

            <div class="flex justify-end space-x-2 rtl:space-x-reverse mt-3">
                <flux:modal.close>
                    <flux:button variant="filled">{{ __('Batal') }}</flux:button>
                </flux:modal.close>

                <flux:button variant="primary" color="emerald" wire:loading.attr="disabled" wire:target="form.photo"
                             type="submit">{{ __('Simpan') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
