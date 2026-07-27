<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class IndikatorKompetensiTemplateSheet implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['Kepemimpinan', 'Kepemimpinan', 'Kemampuan memimpin tim dan mengarahkan bawahan', 25],
            ['Manajemen', 'Pengambilan Keputusan', 'Kemampuan mengambil keputusan yang tepat', 20],
            ['Komunikasi', 'Komunikasi Efektif', 'Kemampuan berkomunikasi dengan baik', 20],
            ['Manajemen', 'Manajemen Tim', 'Kemampuan mengelola tim dengan baik', 20],
            ['Problem Solving', 'Problem Solving', 'Kemampuan menyelesaikan masalah', 15],
        ];
    }

    public function headings(): array
    {
        return ['perspektif', 'nama', 'indikator', 'bobot'];
    }
}