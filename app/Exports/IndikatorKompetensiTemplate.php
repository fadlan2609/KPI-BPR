<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class IndikatorKompetensiTemplate implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        return [
            ['Komunikasi', 'Komunikasi Efektif', 'Kemampuan berkomunikasi dengan baik', 25],
            ['Kerjasama', 'Kerjasama Tim', 'Kemampuan bekerjasama dalam tim', 25],
            ['Problem Solving', 'Problem Solving', 'Kemampuan menyelesaikan masalah', 25],
            ['Manajemen', 'Manajemen Waktu', 'Kemampuan mengelola waktu', 25],
        ];
    }

    public function headings(): array
    {
        return [
            'perspektif',
            'nama',
            'indikator',
            'bobot'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet->getStyle('A1:D1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('1F4E79');
        $sheet->getStyle('A1:D1')->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE));
        
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
}