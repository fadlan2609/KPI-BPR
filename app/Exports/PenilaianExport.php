<?php

namespace App\Exports;

use App\Models\HasilPenilaian;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PenilaianExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $periodeId;

    public function __construct($periodeId)
    {
        $this->periodeId = $periodeId;
    }

    public function collection()
    {
        return HasilPenilaian::with(['pegawai', 'pegawai.jabatan', 'predikat'])
            ->where('periode_id', $this->periodeId)
            ->where('status', 'final')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Pegawai',
            'NIP',
            'Jabatan',
            'Nilai Self Assessment',
            'Nilai Atasan Langsung',
            'Nilai Atasan Penilai',
            'Nilai Akhir',
            'Predikat'
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $row->pegawai->nama,
            $row->pegawai->nip,
            $row->pegawai->jabatan->nama ?? '-',
            $row->nilai_self ?? '-',
            $row->nilai_atasan_langsung ?? '-',
            $row->nilai_atasan_penilai ?? '-',
            $row->nilai_akhir,
            $row->predikat->nama ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}