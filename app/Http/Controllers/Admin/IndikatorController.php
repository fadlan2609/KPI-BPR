<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use App\Models\IndikatorKPI;
use App\Models\IndikatorKompetensi;
use App\Models\IndikatorCoreValue;
use App\Models\BobotPenilaian;
use App\Models\PeriodePenilaian;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\IndikatorKPIImport;
use App\Imports\IndikatorKompetensiImport;
use App\Imports\IndikatorCoreValuesImport;
use App\Imports\IndikatorMultiSheetImport;
use App\Exports\IndikatorKPITemplate;
use App\Exports\IndikatorKompetensiTemplate;
use App\Exports\IndikatorCoreValuesTemplate;
use App\Exports\IndikatorMultiSheetTemplate;

class IndikatorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jabatan = Jabatan::with(['atasan', 'atasan.atasan'])
            ->withCount(['pegawai', 'indikatorKPI', 'indikatorKompetensi', 'indikatorCoreValues'])
            ->paginate(10);
        
        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();
        
        // Hitung status indikator untuk setiap jabatan
        foreach ($jabatan as $j) {
            $totalIndikator = $j->indikator_kpi_count + $j->indikator_kompetensi_count + $j->indikator_core_values_count;
            
            if ($totalIndikator > 0) {
                $j->status_indikator = 'lengkap';
                $j->status_text = '✅ Ada Indikator';
                $j->status_color = 'green';
                $j->total_indikator = $totalIndikator;
            } else {
                $j->status_indikator = 'kosong';
                $j->status_text = '❌ Belum Ada';
                $j->status_color = 'red';
                $j->total_indikator = 0;
            }
        }
        
        return view('admin.indikator.index', compact('jabatan', 'periodeAktif'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($jabatanId)
    {
        $jabatan = Jabatan::with(['atasan', 'atasan.atasan', 'pegawai'])->findOrFail($jabatanId);
        
        $indikatorKPI = IndikatorKPI::where('jabatan_id', $jabatanId)->get();
        $indikatorKompetensi = IndikatorKompetensi::where('jabatan_id', $jabatanId)->get();
        $indikatorCoreValues = IndikatorCoreValue::where('jabatan_id', $jabatanId)->get();
        
        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();
        $bobot = BobotPenilaian::where('jabatan_id', $jabatanId)
            ->where('periode_id', $periodeAktif?->id)
            ->first();
        
        // Bobot default jika belum ada
        if (!$bobot) {
            $bobot = (object) [
                'bobot_kpi' => 70,
                'bobot_kompetensi' => 15,
                'bobot_core_values' => 15,
                'bobot_self' => 20,
                'bobot_atasan_langsung' => 50,
                'bobot_atasan_penilai' => 30,
            ];
        }
        
        return view('admin.indikator.edit', compact(
            'jabatan', 
            'indikatorKPI', 
            'indikatorKompetensi', 
            'indikatorCoreValues',
            'bobot',
            'periodeAktif'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $jabatanId)
    {
        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();
        
        if (!$periodeAktif) {
            return redirect()->back()->with('error', 'Tidak ada periode aktif!');
        }
        
        // Update bobot
        $bobot = BobotPenilaian::updateOrCreate(
            [
                'jabatan_id' => $jabatanId,
                'periode_id' => $periodeAktif->id,
            ],
            [
                'bobot_kpi' => $request->bobot_kpi,
                'bobot_kompetensi' => $request->bobot_kompetensi,
                'bobot_core_values' => $request->bobot_core_values,
                'bobot_self' => $request->bobot_self ?? 20,
                'bobot_atasan_langsung' => $request->bobot_atasan_langsung ?? 50,
                'bobot_atasan_penilai' => $request->bobot_atasan_penilai ?? 30,
            ]
        );
        
        // ==================== UPDATE INDIKATOR KPI ====================
        if ($request->has('indikator_kpi')) {
            // Ambil ID yang ada (bukan new_)
            $existingIds = [];
            foreach ($request->indikator_kpi as $id => $data) {
                if (!str_starts_with($id, 'new_')) {
                    $existingIds[] = $id;
                }
            }
            
            // Hapus indikator yang tidak ada di request
            IndikatorKPI::where('jabatan_id', $jabatanId)
                ->whereNotIn('id', $existingIds)
                ->delete();
            
            // Update atau create indikator
            foreach ($request->indikator_kpi as $id => $data) {
                if (str_starts_with($id, 'new_')) {
                    // Indikator baru
                    IndikatorKPI::create([
                        'jabatan_id' => $jabatanId,
                        'perspektif' => $data['perspektif'] ?? 'Keuangan',
                        'nama' => $data['nama'] ?? 'KPI',
                        'indikator' => $data['indikator'],
                        'target' => 100,
                        'satuan' => 'Persen',
                        'bobot' => $data['bobot'] ?? 0,
                        'periode' => 'bulanan',
                    ]);
                } else {
                    // Update indikator existing
                    $indikator = IndikatorKPI::find($id);
                    if ($indikator) {
                        $indikator->update([
                            'perspektif' => $data['perspektif'] ?? 'Keuangan',
                            'nama' => $data['nama'] ?? 'KPI',
                            'indikator' => $data['indikator'],
                            'bobot' => $data['bobot'] ?? 0,
                        ]);
                    }
                }
            }
        }
        
        // ==================== UPDATE INDIKATOR KOMPETENSI ====================
        if ($request->has('indikator_kompetensi')) {
            $existingIds = [];
            foreach ($request->indikator_kompetensi as $id => $data) {
                if (!str_starts_with($id, 'new_')) {
                    $existingIds[] = $id;
                }
            }
            
            IndikatorKompetensi::where('jabatan_id', $jabatanId)
                ->whereNotIn('id', $existingIds)
                ->delete();
            
            foreach ($request->indikator_kompetensi as $id => $data) {
                if (str_starts_with($id, 'new_')) {
                    IndikatorKompetensi::create([
                        'jabatan_id' => $jabatanId,
                        'perspektif' => $data['perspektif'] ?? 'Komunikasi',
                        'nama' => $data['nama'] ?? 'Kompetensi',
                        'indikator' => $data['indikator'],
                        'skala_maksimal' => 5,
                        'bobot' => $data['bobot'] ?? 0,
                    ]);
                } else {
                    $indikator = IndikatorKompetensi::find($id);
                    if ($indikator) {
                        $indikator->update([
                            'perspektif' => $data['perspektif'] ?? 'Komunikasi',
                            'nama' => $data['nama'] ?? 'Kompetensi',
                            'indikator' => $data['indikator'],
                            'bobot' => $data['bobot'] ?? 0,
                        ]);
                    }
                }
            }
        }
        
        // ==================== UPDATE INDIKATOR CORE VALUES ====================
        if ($request->has('indikator_core_values')) {
            $existingIds = [];
            foreach ($request->indikator_core_values as $id => $data) {
                if (!str_starts_with($id, 'new_')) {
                    $existingIds[] = $id;
                }
            }
            
            IndikatorCoreValue::where('jabatan_id', $jabatanId)
                ->whereNotIn('id', $existingIds)
                ->delete();
            
            foreach ($request->indikator_core_values as $id => $data) {
                if (str_starts_with($id, 'new_')) {
                    IndikatorCoreValue::create([
                        'jabatan_id' => $jabatanId,
                        'perspektif' => $data['perspektif'] ?? 'Service Excellence',
                        'nama' => $data['nama'] ?? 'Core Value',
                        'indikator' => $data['indikator'],
                        'skala_maksimal' => 5,
                        'bobot' => $data['bobot'] ?? 0,
                    ]);
                } else {
                    $indikator = IndikatorCoreValue::find($id);
                    if ($indikator) {
                        $indikator->update([
                            'perspektif' => $data['perspektif'] ?? 'Service Excellence',
                            'nama' => $data['nama'] ?? 'Core Value',
                            'indikator' => $data['indikator'],
                            'bobot' => $data['bobot'] ?? 0,
                        ]);
                    }
                }
            }
        }
        
        return redirect()->route('admin.indikator.index')
            ->with('success', 'Indikator penilaian berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $jenis = request('jenis');
        
        try {
            if ($jenis == 'kpi') {
                $indikator = IndikatorKPI::findOrFail($id);
            } elseif ($jenis == 'kompetensi') {
                $indikator = IndikatorKompetensi::findOrFail($id);
            } elseif ($jenis == 'core_values') {
                $indikator = IndikatorCoreValue::findOrFail($id);
            } else {
                return response()->json(['success' => false, 'message' => 'Jenis indikator tidak valid'], 400);
            }
            
            $indikator->delete();
            
            return response()->json(['success' => true, 'message' => 'Indikator berhasil dihapus!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus indikator: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Import indikator from Excel (Single Sheet)
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120',
            'jenis' => 'required|in:kpi,kompetensi,core_values',
            'jabatan_id' => 'required|exists:jabatan,id'
        ]);
        
        try {
            $jabatanId = $request->jabatan_id;
            $jenis = $request->jenis;
            
            // Pilih import class berdasarkan jenis
            switch ($jenis) {
                case 'kpi':
                    $import = new IndikatorKPIImport($jabatanId);
                    break;
                case 'kompetensi':
                    $import = new IndikatorKompetensiImport($jabatanId);
                    break;
                case 'core_values':
                    $import = new IndikatorCoreValuesImport($jabatanId);
                    break;
                default:
                    return redirect()->back()
                        ->with('error', 'Jenis indikator tidak valid!');
            }
            
            Excel::import($import, $request->file('file'));
            
            // Cek jika ada error
            if (method_exists($import, 'failures') && $import->failures()->count() > 0) {
                $errors = [];
                foreach ($import->failures() as $failure) {
                    $errors[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
                }
                return redirect()->back()
                    ->with('warning', 'Import selesai dengan beberapa error: ' . implode(' | ', $errors));
            }
            
            return redirect()->back()
                ->with('success', 'Indikator berhasil diimport!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    /**
     * Import multi sheet Excel (KPI, Kompetensi, Core Values)
     */
    public function importMulti(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120',
            'jabatan_id' => 'required|exists:jabatan,id'
        ]);

        try {
            $jabatanId = $request->jabatan_id;
            
            // Hapus semua indikator lama untuk jabatan ini
            IndikatorKPI::where('jabatan_id', $jabatanId)->delete();
            IndikatorKompetensi::where('jabatan_id', $jabatanId)->delete();
            IndikatorCoreValue::where('jabatan_id', $jabatanId)->delete();

            $import = new IndikatorMultiSheetImport($jabatanId);
            Excel::import($import, $request->file('file'));

            return redirect()->back()
                ->with('success', 'Semua indikator berhasil diimport! (KPI, Kompetensi, Core Values)');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    /**
     * Download template Excel for import (Single Sheet)
     */
    public function template($jenis)
    {
        switch ($jenis) {
            case 'kpi':
                return Excel::download(new IndikatorKPITemplate, 'template_indikator_kpi.xlsx');
            case 'kompetensi':
                return Excel::download(new IndikatorKompetensiTemplate, 'template_indikator_kompetensi.xlsx');
            case 'core_values':
                return Excel::download(new IndikatorCoreValuesTemplate, 'template_indikator_core_values.xlsx');
            default:
                abort(404);
        }
    }

    /**
     * Download multi sheet template
     */
    public function templateMulti()
    {
        return Excel::download(new IndikatorMultiSheetTemplate, 'template_indikator_all.xlsx');
    }

    /**
     * API: Get indikator by jabatan
     */
    public function apiByJabatan($jabatanId)
    {
        $kpi = IndikatorKPI::where('jabatan_id', $jabatanId)->get();
        $kompetensi = IndikatorKompetensi::where('jabatan_id', $jabatanId)->get();
        $coreValues = IndikatorCoreValue::where('jabatan_id', $jabatanId)->get();
        
        return response()->json([
            'kpi' => $kpi,
            'kompetensi' => $kompetensi,
            'core_values' => $coreValues,
        ]);
    }

    /**
     * API: Get KPI by jabatan
     */
    public function apiKPI($jabatanId)
    {
        $data = IndikatorKPI::where('jabatan_id', $jabatanId)->get();
        return response()->json($data);
    }

    /**
     * API: Get Kompetensi by jabatan
     */
    public function apiKompetensi($jabatanId)
    {
        $data = IndikatorKompetensi::where('jabatan_id', $jabatanId)->get();
        return response()->json($data);
    }

    /**
     * API: Get Core Values by jabatan
     */
    public function apiCoreValues($jabatanId)
    {
        $data = IndikatorCoreValue::where('jabatan_id', $jabatanId)->get();
        return response()->json($data);
    }
}