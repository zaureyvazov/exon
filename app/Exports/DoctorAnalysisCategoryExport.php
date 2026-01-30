<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class DoctorAnalysisCategoryExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $reportData;
    protected $categories;
    protected $startDate;
    protected $endDate;

    public function __construct($reportData, $categories, $startDate, $endDate)
    {
        $this->reportData = $reportData;
        $this->categories = $categories;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $rows = collect();

        // Add date range info
        $rows->push([
            'Tarix Aralığı: ' . \Carbon\Carbon::parse($this->startDate)->format('d.m.Y') . ' - ' . \Carbon\Carbon::parse($this->endDate)->format('d.m.Y')
        ]);
        $rows->push([]); // Empty row

        // Add data rows
        foreach ($this->reportData as $data) {
            $row = [$data['doctor']];

            foreach ($this->categories as $category) {
                $row[] = $data['categories'][$category->name] ?? 0;
            }

            $row[] = $data['total'];
            $rows->push($row);
        }

        // Add totals row
        $totalsRow = ['ÜMUMI'];
        foreach ($this->categories as $category) {
            $categoryTotal = 0;
            foreach ($this->reportData as $data) {
                $categoryTotal += $data['categories'][$category->name] ?? 0;
            }
            $totalsRow[] = $categoryTotal;
        }

        $grandTotal = 0;
        foreach ($this->reportData as $data) {
            $grandTotal += $data['total'];
        }
        $totalsRow[] = $grandTotal;

        $rows->push($totalsRow);

        return $rows;
    }

    public function headings(): array
    {
        $headings = ['Doktor'];

        foreach ($this->categories as $category) {
            $headings[] = $category->name;
        }

        $headings[] = 'CƏMI';

        return $headings;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header row styling
            3 => [
                'font' => ['bold' => true, 'size' => 12],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            // Date range row
            1 => [
                'font' => ['bold' => true, 'size' => 11],
            ],
        ];
    }

    public function title(): string
    {
        return 'Doktor Analiz Növü Raporu';
    }
}
