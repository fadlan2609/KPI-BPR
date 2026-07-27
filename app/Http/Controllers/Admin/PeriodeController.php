<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodePenilaian;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PeriodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $periode = PeriodePenilaian::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.periode.index', compact('periode'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.periode.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama' => 'required|string|max:100|unique:periode_penilaian',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'batas_self_assessment' => 'required|date|after_or_equal:tanggal_mulai|before_or_equal:tanggal_selesai',
            'batas_penilaian_atasan' => 'required|date|after_or_equal:batas_self_assessment|before_or_equal:tanggal_selesai',
            'batas_finalisasi' => 'required|date|after_or_equal:batas_penilaian_atasan|before_or_equal:tanggal_selesai',
        ]);

        try {
            // Nonaktifkan periode lain jika ini aktif
            if ($request->has('is_active')) {
                PeriodePenilaian::where('is_active', true)->update(['is_active' => false]);
            }

            // Buat periode baru
            $periode = PeriodePenilaian::create([
                'nama' => $request->nama,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'batas_self_assessment' => $request->batas_self_assessment,
                'batas_penilaian_atasan' => $request->batas_penilaian_atasan,
                'batas_finalisasi' => $request->batas_finalisasi,
                'status' => $request->status ?? 'active',
                'is_active' => $request->has('is_active') ? true : false,
            ]);

            return redirect()->route('admin.periode.index')
                ->with('success', 'Periode penilaian "' . $periode->nama . '" berhasil ditambahkan!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menyimpan periode: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $periode = PeriodePenilaian::findOrFail($id);
        return view('admin.periode.show', compact('periode'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $periode = PeriodePenilaian::findOrFail($id);
        return view('admin.periode.edit', compact('periode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $periode = PeriodePenilaian::findOrFail($id);

        // Validasi input
        $request->validate([
            'nama' => 'required|string|max:100|unique:periode_penilaian,nama,' . $id,
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'batas_self_assessment' => 'required|date|after_or_equal:tanggal_mulai|before_or_equal:tanggal_selesai',
            'batas_penilaian_atasan' => 'required|date|after_or_equal:batas_self_assessment|before_or_equal:tanggal_selesai',
            'batas_finalisasi' => 'required|date|after_or_equal:batas_penilaian_atasan|before_or_equal:tanggal_selesai',
        ]);

        try {
            // Nonaktifkan periode lain jika ini aktif
            if ($request->has('is_active')) {
                PeriodePenilaian::where('is_active', true)->where('id', '!=', $id)->update(['is_active' => false]);
            }

            // Update periode
            $periode->update([
                'nama' => $request->nama,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'batas_self_assessment' => $request->batas_self_assessment,
                'batas_penilaian_atasan' => $request->batas_penilaian_atasan,
                'batas_finalisasi' => $request->batas_finalisasi,
                'status' => $request->status ?? 'active',
                'is_active' => $request->has('is_active') ? true : false,
            ]);

            return redirect()->route('admin.periode.index')
                ->with('success', 'Periode penilaian "' . $periode->nama . '" berhasil diperbarui!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui periode: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $periode = PeriodePenilaian::findOrFail($id);
            
            // Cek apakah ada penilaian yang terkait
            if ($periode->penilaian()->count() > 0) {
                return redirect()->route('admin.periode.index')
                    ->with('error', 'Periode tidak bisa dihapus karena sudah ada penilaian!');
            }

            $periode->delete();

            return redirect()->route('admin.periode.index')
                ->with('success', 'Periode penilaian berhasil dihapus!');

        } catch (\Exception $e) {
            return redirect()->route('admin.periode.index')
                ->with('error', 'Gagal menghapus periode: ' . $e->getMessage());
        }
    }

    /**
     * Activate a periode
     */
    public function activate($id)
    {
        try {
            // Nonaktifkan semua periode
            PeriodePenilaian::where('is_active', true)->update(['is_active' => false]);
            
            // Aktifkan periode yang dipilih
            $periode = PeriodePenilaian::findOrFail($id);
            $periode->update([
                'is_active' => true,
                'status' => 'active'
            ]);

            return redirect()->route('admin.periode.index')
                ->with('success', 'Periode penilaian "' . $periode->nama . '" berhasil diaktifkan!');

        } catch (\Exception $e) {
            return redirect()->route('admin.periode.index')
                ->with('error', 'Gagal mengaktifkan periode: ' . $e->getMessage());
        }
    }

    /**
     * Deactivate a periode
     */
    public function deactivate($id)
    {
        try {
            $periode = PeriodePenilaian::findOrFail($id);
            $periode->update([
                'is_active' => false,
                'status' => 'draft'
            ]);

            return redirect()->route('admin.periode.index')
                ->with('success', 'Periode penilaian "' . $periode->nama . '" berhasil dinonaktifkan!');

        } catch (\Exception $e) {
            return redirect()->route('admin.periode.index')
                ->with('error', 'Gagal menonaktifkan periode: ' . $e->getMessage());
        }
    }

    /**
     * Get active periode (for API/AJAX)
     */
    public function getActive()
    {
        $periode = PeriodePenilaian::where('is_active', true)->first();
        
        if ($periode) {
            return response()->json([
                'success' => true,
                'data' => $periode
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Tidak ada periode aktif'
        ]);
    }

    /**
     * Get all periode (for API/AJAX)
     */
    public function getAll()
    {
        $periode = PeriodePenilaian::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $periode
        ]);
    }

    /**
     * Check if periode is active
     */
    public function checkActive()
    {
        $periode = PeriodePenilaian::where('is_active', true)->first();
        
        return response()->json([
            'success' => true,
            'is_active' => $periode ? true : false,
            'periode' => $periode
        ]);
    }
}