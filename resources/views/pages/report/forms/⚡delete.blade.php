<?php

use Flux\Flux;
use Livewire\Component;
use App\Livewire\Forms\ReportForm;

new class extends Component {
    public ReportForm $form;
    public $reportId;

    public function mount($reportId): void
    {
        $this->reportId = $reportId;
    }

    public function delete(): void
    {
        $this->form->delete($this->reportId);
        $this->dispatch('report-deleted');
        $this->dispatch('$refresh')->to('pages::report.index');

        Flux::modals()->close();
    }
};
?>

<div>
    <flux:modal :name="'delete-report-' . $this->reportId"
                :show="$errors->isNotEmpty()"
                focusable
                :dismissible="false">
        <form method="POST" wire:submit.prevent="delete" class="space-y-6">
            <flux:heading size="lg">{{ __('Hapus Data') }}</flux:heading>

            <flux:subheading>
                {{ __('Data yang telah dihapus tidak dapat dipulihkan, yakin ingin menghapus data?') }}
            </flux:subheading>

            <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                <flux:modal.close>
                    <flux:button variant="filled">{{ __('Batal') }}</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" color="red" type="submit">{{ __('Hapus') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
