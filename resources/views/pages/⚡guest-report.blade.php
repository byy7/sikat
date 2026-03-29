<?php

use App\Livewire\Forms\ReportForm;
use App\Services\ReportService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Report;

new #[Layout('layouts::auth')]
class extends Component {
    use WithFileUploads;

    #[Validate('required')]
    public $name = '';

    #[Validate('required')]
    public $necessary = '';

    #[Validate('nullable|image')]
    public $photo = null;

    public function save()
    {
        $this->validate();

        /* Handle photo */
        $reportService = new ReportService;
        $photoPath = $reportService->saveImage($this->photo);

        $report = Report::create([
            'name' => $this->name,
            'necessary' => $this->necessary,
            'photo' => $photoPath,
        ]);

        $this->reset();
        $this->dispatch('report-saved');

        return $this->redirectRoute('reports.guest.completed', encrypt($report->id), true, true);
    }
};
?>
<div>
    <div class="absolute inset-0 -z-10 bg-cover bg-center bg-no-repeat"
         style="background-image: url('{{ asset('assets/img/gedung1.webp') }}');opacity: 35%"></div>

    <form method="POST" wire:submit="save" class="space-y-6">
        <flux:heading size="lg">{{ __('Data Pengunjung') }}</flux:heading>

        <flux:subheading>
            {{ __('Tambah Data Pengunjung') }}
        </flux:subheading>

        <flux:field>
            <flux:label badge="Wajib">Nama</flux:label>
            <flux:input type="text" autocomplete="off" wire:model="name" required/>
            <flux:error name="name"/>
        </flux:field>
        <flux:field>
            <flux:label badge="Wajib">Keperluan</flux:label>
            <flux:select wire:model="necessary" placeholder="Keperluan..." required>
                @foreach(Report::NECESSARY_CHOICE as $key => $value)
                    <flux:select.option value="{{ $key }}">{{ $value }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:error name="necessary"/>
        </flux:field>

        <div
            x-data="{ uploading: false, progress: 0 }"
            x-on:livewire-upload-start="uploading = true"
            x-on:livewire-upload-finish="uploading = false"
            x-on:livewire-upload-cancel="uploading = false"
            x-on:livewire-upload-error="uploading = false"
            x-on:livewire-upload-progress="progress = $event.detail.progress">

            <flux:input type="file" wire:model="photo" label="Foto" accept="image/*" capture="environment"
                        description:trailing="Foto wajib format JPG/JPEG/PNG." required/>

            <div wire:loading wire:target="photo">Uploading...</div>

            <!-- Progress Bar -->
            <div x-show="uploading">
                <progress max="100" x-bind:value="progress"></progress>
            </div>
        </div>
        <x-action-message class="mt-2 mb-2" on="report-saved">
            <flux:badge icon="check-circle" color="lime">Data berhasil tersimpan.</flux:badge>
        </x-action-message>

        <div class="flex justify-end space-x-2 rtl:space-x-reverse mt-3">
            <flux:button variant="primary" color="emerald" wire:loading.attr="disabled" wire:target="photo"
                         type="submit">{{ __('Simpan') }}</flux:button>
        </div>
    </form>
</div>
