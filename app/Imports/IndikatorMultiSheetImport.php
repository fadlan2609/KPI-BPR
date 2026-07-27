<?php

namespace App\Imports;

use App\Models\IndikatorKPI;
use App\Models\IndikatorKompetensi;
use App\Models\IndikatorCoreValue;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Illuminate\Support\Facades\DB;

class IndikatorMultiSheetImport implements WithMultipleSheets, SkipsUnknownSheets
{
    protected $jabatanId;

    public function __construct($jabatanId)
    {
        $this->jabatanId = $jabatanId;
    }

    public function sheets(): array
    {
        return [
            'KPI' => new IndikatorKPISheetImport($this->jabatanId),
            'Kompetensi' => new IndikatorKompetensiSheetImport($this->jabatanId),
            'Core Values' => new IndikatorCoreValuesSheetImport($this->jabatanId),
        ];
    }

    public function onUnknownSheet($sheetName)
    {
        // Eror handling jika sheet tidak ditemukan
        throw new \Exception("Sheet '{$sheetName}' tidak ditemukan. Pastikan sheet bernama: KPI, Kompetensi, Core Values");
    }
}