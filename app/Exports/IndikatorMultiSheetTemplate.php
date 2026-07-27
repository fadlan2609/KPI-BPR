<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class IndikatorMultiSheetTemplate implements WithMultipleSheets, WithStyles
{
    public function sheets(): array
    {
        return [
            'KPI' => new IndikatorKPITemplateSheet(),
            'Kompetensi' => new IndikatorKompetensiTemplateSheet(),
            'Core Values' => new IndikatorCoreValuesTemplateSheet(),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style untuk semua sheet
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet->getStyle('A1:D1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('1F4E79');
        $sheet->getStyle('A1:D1')->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE));
        
        // Auto size columns
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
}