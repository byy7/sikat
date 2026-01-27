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
    public $photo;

    public function setReport(Report $report): void
    {
        $this->report = $report;
        $this->name = $report->name;
        $this->necessary = $report->necessary;
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
}
