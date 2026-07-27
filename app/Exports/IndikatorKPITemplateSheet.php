<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class IndikatorKPITemplateSheet implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['Keuangan', 'Pencapaian Target Bisnis', 'Mencapai target bisnis yang ditentukan', 30],
            ['Keuangan', 'Pertumbuhan Portofolio', 'Meningkatkan portofolio bisnis', 25],
            ['Keuangan', 'ROA', 'Mencapai target ROA yang ditentukan', 15],
            ['Keuangan', 'ROE', 'Mencapai target ROE yang ditentukan', 15],
            ['Pertumbuhan', 'Pengembangan Cabang', 'Mengembangkan jaringan cabang', 15],
        ];
    }

    public function headings(): array
    {
        return ['perspektif', 'nama', 'indikator', 'bobot'];
    }
}