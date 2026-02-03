<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $reports;

    public function __construct($reports)
    {
        $this->reports = $reports;
    }

    public function collection(): Collection
    {
        return $this->reports;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Nama',
            'Keperluan',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $row->created_at->format('d/m/Y'),
            $row->name,
            $row->necessary,
        ];
    }

    public function title(): string
    {
        return 'Laporan Pengunjung';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
