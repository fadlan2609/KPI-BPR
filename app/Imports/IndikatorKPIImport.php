<?php

namespace App\Imports;

use App\Models\IndikatorKPI;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class IndikatorKPIImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    protected $jabatanId;

    public function __construct($jabatanId)
    {
        $this->jabatanId = $jabatanId;
    }

    public function model(array $row)
    {
        return new IndikatorKPI([
            'jabatan_id' => $this->jabatanId,
            'perspektif' => $row['perspektif'] ?? 'Keuangan',
            'nama' => $row['nama'] ?? $row['kpi'] ?? 'KPI',
            'indikator' => $row['indikator'],
            'target' => 100, // Default value
            'satuan' => 'Persen', // Default value
            'bobot' => $row['bobot'] ?? 0,
            'periode' => 'bulanan',
        ]);
    }

    public function rules(): array
    {
        return [
            'perspektif' => 'nullable|string|max:100',
            'nama' => 'nullable|string|max:255',
            'kpi' => 'nullable|string|max:255',
            'indikator' => 'required|string|max:500',
            'bobot' => 'required|numeric|min:0|max:100',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'indikator.required' => 'Kolom indikator wajib diisi',
            'bobot.required' => 'Kolom bobot wajib diisi',
            'bobot.numeric' => 'Bobot harus berupa angka',
            'bobot.min' => 'Bobot minimal 0',
            'bobot.max' => 'Bobot maksimal 100',
        ];
    }
}