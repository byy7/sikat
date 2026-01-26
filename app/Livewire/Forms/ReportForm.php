<?php

namespace App\Livewire\Forms;

use App\Models\Report;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ReportForm extends Form
{
    public ?Report $report;

    #[Validate('required')]
    public $name = '';

    #[Validate('required')]
    public $necessary = '';

    #[Validate('nullable|mimes:jpg,jpeg,png|max:5120')]
    public $photo = '';

    public function setReport(Report $report)
    {
        $this->report = $report;
        $this->name = $report->name;
        $this->necessary = $report->necessary;
        $this->photo = $report->photo;
    }

    public function store()
    {
        $this->validate();

        Report::create($this->pull());
    }

    public function update()
    {
        $this->validate();

        $this->report->update($this->pull());
    }
}
