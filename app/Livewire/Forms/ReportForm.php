<?php

namespace App\Livewire\Forms;

use App\Models\Report;
use App\Services\ReportService;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ReportForm extends Form
{
    public ?Report $report;

    #[Validate('required')]
    public $name = '';

    #[Validate('required')]
    public $necessary = '';

    #[Validate('required|image|max:5120')]
    public $photo = null;

    public function setReport($id): void
    {
        $id = decrypt($id);
        $this->report = Report::find($id);
        $this->name = $this->report->name;
        $this->necessary = $this->report->necessary;
    }

    public function store(): void
    {
        $this->validate();

        /* Handle photo */
        $reportService = new ReportService;
        $photoPath = $reportService->saveImage($this->photo);

        Report::create([
            'name' => $this->name,
            'necessary' => $this->necessary,
            'photo' => $photoPath,
        ]);

        $this->reset();
    }

    public function update(): void
    {
        $this->validate();

        $this->report->update($this->pull());
    }

    public function delete($id): void
    {
        $report = Report::find(decrypt($id));

        /* Delete images */

        $report->delete();
    }
}
