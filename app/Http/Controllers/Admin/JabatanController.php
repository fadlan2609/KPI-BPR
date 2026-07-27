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
    public function index()
    {
        $jabatan = Jabatan::with('atasan')->paginate(10);
        $jabatanOptions = Jabatan::all();
        return view('admin.jabatan.index', compact('jabatan', 'jabatanOptions'));
    }

    public function create()
    {
        $jabatanOptions = Jabatan::all();
        return view('admin.jabatan.create', compact('jabatanOptions'));
    }

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

    public function edit($id)
    {
        $jabatan = Jabatan::findOrFail($id);
        $jabatanOptions = Jabatan::where('id', '!=', $id)->get();
        return view('admin.jabatan.edit', compact('jabatan', 'jabatanOptions'));
    }

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

    public function template()
    {
        return Excel::download(new JabatanExport, 'template_jabatan.xlsx');
    }
}