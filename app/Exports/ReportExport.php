<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    protected $data;
    protected $reportType;
    protected $startDate;
    protected $endDate;

    public function __construct($data, $reportType, $startDate, $endDate)
    {
        $this->data = $data;
        $this->reportType = $reportType;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        switch ($this->reportType) {
            case 'financial-summary':
                return ['Metrik', 'Məbləğ (AZN)'];

            case 'daily-revenue':
                return ['Tarix', 'Gəlir (AZN)', 'Komissiyalar (AZN)', 'Xalis Gəlir (AZN)', 'Göndəriş Sayı'];

            case 'monthly-revenue':
                return ['Ay', 'Gəlir (AZN)', 'Komissiyalar (AZN)', 'Xalis Gəlir (AZN)', 'Göndəriş Sayı'];

            case 'patient-statistics':
                return ['Həkim', 'Xəstə Sayı'];

            case 'repeat-patients':
                return ['Xəstə Adı', 'FIN', 'Qeydiyyatçı Həkim', 'Göndəriş Sayı'];

            case 'popular-analyses':
                return ['Analiz Adı', 'İstifadə Sayı'];

            case 'analysis-revenue':
                return ['Analiz Adı', 'İstifadə Sayı', 'Toplam Gəlir (AZN)', 'Orta Qiymət (AZN)'];

            case 'analysis-by-category':
                return ['Analiz Növü', 'İstifadə Sayı', 'Toplam Gəlir (AZN)'];

            case 'doctor-performance':
                return ['Həkim', 'Göndəriş Sayı', 'Toplam Gəlir (AZN)', 'Toplam Komissiya (AZN)', 'Orta Göndəriş Dəyəri (AZN)'];

            case 'doctor-ranking':
                return ['Sıra', 'Həkim', 'Göndəriş Sayı', 'Xəstə Sayı', 'Toplam Komissiya (AZN)', 'Xal'];

            case 'discount-report':
                return ['Xəstə', 'Həkim', 'Tarix', 'Analiz Sayı', 'Son Qiymət (AZN)', 'Komissiya (AZN)'];

            case 'referral-status':
                return ['Status', 'Sayı'];

            default:
                return ['Məlumat'];
        }
    }

    public function map($row): array
    {
        switch ($this->reportType) {
            case 'financial-summary':
                return [$row['label'], number_format($row['value'], 2)];

            case 'daily-revenue':
                return [
                    $row['date'],
                    number_format($row['revenue'], 2),
                    number_format($row['commissions'], 2),
                    number_format($row['net_profit'], 2),
                    $row['referral_count']
                ];

            case 'monthly-revenue':
                return [
                    $row['month'],
                    number_format($row['revenue'], 2),
                    number_format($row['commissions'], 2),
                    number_format($row['net_profit'], 2),
                    $row['referral_count']
                ];

            case 'patient-statistics':
                return [
                    $row['doctor_name'],
                    $row['patient_count']
                ];

            case 'repeat-patients':
                return [
                    $row['patient_name'],
                    $row['fin'],
                    $row['doctor_name'],
                    $row['referral_count']
                ];

            case 'popular-analyses':
                return [
                    $row['name'],
                    $row['usage_count']
                ];

            case 'analysis-revenue':
                return [
                    $row['name'],
                    $row['usage_count'],
                    number_format($row['total_revenue'], 2),
                    number_format($row['avg_price'], 2)
                ];

            case 'analysis-by-category':
                return [
                    $row['name'],
                    $row['usage_count'],
                    number_format($row['total_revenue'], 2)
                ];

            case 'doctor-performance':
                return [
                    $row['doctor_name'],
                    $row['referral_count'],
                    number_format($row['total_revenue'], 2),
                    number_format($row['total_commission'], 2),
                    number_format($row['avg_referral_value'], 2)
                ];

            case 'doctor-ranking':
                return [
                    $row['rank'],
                    $row['doctor_name'],
                    $row['referral_count'],
                    $row['patient_count'],
                    number_format($row['total_commission'], 2),
                    number_format($row['score'], 2)
                ];

            case 'discount-report':
                return [
                    $row['patient_name'],
                    $row['doctor_name'],
                    $row['date'],
                    $row['analysis_count'],
                    number_format($row['final_price'], 2),
                    number_format($row['commission'], 2)
                ];

            case 'referral-status':
                return [
                    $row['status'],
                    $row['count']
                ];

            default:
                return [$row];
        }
    }

    public function title(): string
    {
        return 'Raport';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
