<?php

namespace App\Livewire\Forms;

use App\Models\Report;
use App\Services\ReportService;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ReportForm extends Form
{
    public ?Report $report;

    #[Validate('required')]
    public $name = '';

    #[Validate('required')]
    public $necessary = '';

    #[Validate('nullable|image|max:5120')]
    public $photo = null;

    public function setReport($id): void
    {
        $this->report = Report::find(decrypt($id));
        $this->name = $this->report->name;
        $this->necessary = strtolower(str_replace(' ', '_', $this->report->necessary));
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

        if (! is_null($this->photo)) {
            /* Delete Existing Photo */
            $this->removeExistingPhoto($this->report->photo);

            /* Handle photo */
            $reportService = new ReportService;
            $photoPath = $reportService->saveImage($this->photo);
            $this->report->update([
                'name' => $this->name,
                'necessary' => $this->necessary,
                'photo' => $photoPath,
            ]);
        } else {
            $this->report->update([
                'name' => $this->name,
                'necessary' => $this->necessary,
            ]);
            $this->reset();
        }
    }

    public function delete($id): void
    {
        $report = Report::find(decrypt($id));

        /* Delete images */
        $this->removeExistingPhoto($report->photo);

        $report->delete();
    }

    private function removeExistingPhoto($path): void
    {
        $photoPath = Storage::path($path);

        if (file_exists($photoPath)) {
            unlink($photoPath);
        }
    }
}
