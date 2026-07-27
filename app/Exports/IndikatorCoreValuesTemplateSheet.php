<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class IndikatorCoreValuesTemplateSheet implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['Service Excellence', 'Pelayanan Prima', 'Memberikan pelayanan terbaik dan tepat waktu', 20],
            ['Target Oriented', 'Berorientasi Target', 'Fokus pada pencapaian target dengan efisien', 20],
            ['Accountability', 'Tanggung Jawab', 'Bertanggung jawab menuntaskan pekerjaan', 20],
            ['Reliable', 'Dapat Dipercaya', 'Menjadi pribadi yang dapat dipercaya', 20],
            ['Synergy', 'Sinergi', 'Berkolaborasi dan berkontribusi untuk tim', 20],
        ];
    }

    public function headings(): array
    {
        return ['perspektif', 'nama', 'indikator', 'bobot'];
    }
}