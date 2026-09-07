<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\JabatanImport;
use App\Exports\JabatanExport;

class JabatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        
        $jabatan = Jabatan::with('atasan')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('nama', 'LIKE', "%{$search}%")
                      ->orWhere('keterangan', 'LIKE', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        $jabatanOptions = Jabatan::all();
        
        return view('admin.jabatan.index', compact('jabatan', 'jabatanOptions', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $jabatanOptions = Jabatan::all();
        return view('admin.jabatan.create', compact('jabatanOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100|unique:jabatan',
            'jabatan_atasan_id' => 'nullable|exists:jabatan,id',
            'keterangan' => 'nullable|string'
        ]);
        
        Jabatan::create($request->all());
        
        return redirect()->route('admin.jabatan.index')
            ->with('success', 'Jabatan berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $jabatan = Jabatan::with(['atasan', 'bawahan', 'pegawai'])->findOrFail($id);
        return view('admin.jabatan.show', compact('jabatan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $jabatan = Jabatan::findOrFail($id);
        $jabatanOptions = Jabatan::where('id', '!=', $id)->get();
        return view('admin.jabatan.edit', compact('jabatan', 'jabatanOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $jabatan = Jabatan::findOrFail($id);
        
        $request->validate([
            'nama' => 'required|string|max:100|unique:jabatan,nama,' . $id,
            'jabatan_atasan_id' => 'nullable|exists:jabatan,id',
            'keterangan' => 'nullable|string'
        ]);
        
        $jabatan->update($request->all());
        
        return redirect()->route('admin.jabatan.index')
            ->with('success', 'Jabatan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $jabatan = Jabatan::findOrFail($id);
        
        // Cek apakah ada pegawai dengan jabatan ini
        if ($jabatan->pegawai()->count() > 0) {
            return redirect()->route('admin.jabatan.index')
                ->with('error', 'Jabatan tidak bisa dihapus karena masih ada pegawai!');
        }
        
        // Cek apakah ada jabatan lain yang menjadikan ini sebagai atasan
        if ($jabatan->bawahan()->count() > 0) {
            return redirect()->route('admin.jabatan.index')
                ->with('error', 'Jabatan tidak bisa dihapus karena masih menjadi atasan jabatan lain!');
        }
        
        $jabatan->delete();
        
        return redirect()->route('admin.jabatan.index')
            ->with('success', 'Jabatan berhasil dihapus!');
    }

    /**
     * Import jabatan from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120'
        ]);
        
        try {
            Excel::import(new JabatanImport, $request->file('file'));
            return redirect()->route('admin.jabatan.index')
                ->with('success', 'Data jabatan berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->route('admin.jabatan.index')
                ->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    /**
     * Download template Excel for import
     */
    public function template()
    {
        return Excel::download(new JabatanExport, 'template_jabatan.xlsx');
    }

    /**
     * Export jabatan to Excel
     */
    public function export()
    {
        $jabatan = Jabatan::with('atasan')->get();
        return Excel::download(new JabatanExport($jabatan), 'data_jabatan.xlsx');
    }

    /**
     * Get jabatan hierarchy (for AJAX)
     */
    public function hierarchy()
    {
        $jabatan = Jabatan::with(['atasan', 'bawahan'])->get();
        return response()->json($jabatan);
    }

    /**
     * Get jabatan list for dropdown (for AJAX)
     */
    public function apiList(Request $request)
    {
        $search = $request->search;
        
        $jabatan = Jabatan::when($search, function ($query, $search) {
                return $query->where('nama', 'LIKE', "%{$search}%");
            })
            ->select('id', 'nama')
            ->orderBy('nama')
            ->get();
        
        return response()->json($jabatan);
    }
}