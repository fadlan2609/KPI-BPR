<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class IndikatorKPITemplate implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        return [
            ['Keuangan', 'Pencapaian Target', 'Mencapai target yang ditentukan', 30],
            ['Keuangan', 'Kualitas Kerja', 'Menjaga kualitas kerja', 25],
            ['Pertumbuhan', 'Inovasi', 'Mengembangkan ide-ide baru', 20],
            ['Pelayanan', 'Kepuasan Pelanggan', 'Menjaga kepuasan pelanggan', 25],
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
        // Style untuk header
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