<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class IndikatorCoreValuesTemplate implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        return [
            ['Service Excellence', 'Pelayanan Prima', 'Memberikan pelayanan terbaik', 20],
            ['Target Oriented', 'Berorientasi Target', 'Fokus pada pencapaian target', 20],
            ['Accountability', 'Tanggung Jawab', 'Bertanggung jawab atas pekerjaan', 20],
            ['Reliable', 'Dapat Dipercaya', 'Menjadi pribadi yang dapat dipercaya', 20],
            ['Synergy', 'Sinergi', 'Berkolaborasi dengan tim', 20],
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