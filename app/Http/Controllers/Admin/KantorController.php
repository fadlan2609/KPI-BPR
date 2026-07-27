<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kantor;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\KantorImport;
use App\Exports\KantorExport;

class KantorController extends Controller
{
    public function index()
    {
        $kantor = Kantor::withCount('pegawai')->paginate(10);
        return view('admin.kantor.index', compact('kantor'));
    }

    public function create()
    {
        return view('admin.kantor.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100|unique:kantor',
            'alamat' => 'nullable|string',
            'keterangan' => 'nullable|string'
        ]);

        Kantor::create($request->all());

        return redirect()->route('admin.kantor.index')
            ->with('success', 'Kantor berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $kantor = Kantor::findOrFail($id);
        return view('admin.kantor.edit', compact('kantor'));
    }

    public function update(Request $request, $id)
    {
        $kantor = Kantor::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:100|unique:kantor,nama,' . $id,
            'alamat' => 'nullable|string',
            'keterangan' => 'nullable|string'
        ]);

        $kantor->update($request->all());

        return redirect()->route('admin.kantor.index')
            ->with('success', 'Kantor berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $kantor = Kantor::findOrFail($id);

        // Cek apakah ada pegawai di kantor ini
        if ($kantor->pegawai()->count() > 0) {
            return redirect()->route('admin.kantor.index')
                ->with('error', 'Kantor tidak bisa dihapus karena masih memiliki pegawai!');
        }

        $kantor->delete();

        return redirect()->route('admin.kantor.index')
            ->with('success', 'Kantor berhasil dihapus!');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120'
        ]);

        try {
            Excel::import(new KantorImport, $request->file('file'));
            return redirect()->route('admin.kantor.index')
                ->with('success', 'Data kantor berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->route('admin.kantor.index')
                ->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function template()
    {
        return Excel::download(new KantorExport, 'template_kantor.xlsx');
    }
}