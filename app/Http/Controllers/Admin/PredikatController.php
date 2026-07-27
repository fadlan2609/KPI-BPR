<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PredikatKinerja;
use Illuminate\Http\Request;

class PredikatController extends Controller
{
    public function index()
    {
        $predikat = PredikatKinerja::paginate(10);
        return view('admin.predikat.index', compact('predikat'));
    }

    public function create()
    {
        return view('admin.predikat.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:50|unique:predikat_kinerja',
            'batas_atas' => 'required|numeric|min:0|max:100',
            'batas_bawah' => 'required|numeric|min:0|max:100',
            'warna_text' => 'required|string|max:7',
            'warna_latar' => 'required|string|max:7',
            'keterangan' => 'nullable|string'
        ]);

        try {
            PredikatKinerja::create($request->all());

            return redirect()->route('admin.predikat.index')
                ->with('success', 'Predikat berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan predikat: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        $predikat = PredikatKinerja::findOrFail($id);
        return view('admin.predikat.edit', compact('predikat'));
    }

    public function update(Request $request, $id)
    {
        $predikat = PredikatKinerja::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:50|unique:predikat_kinerja,nama,' . $id,
            'batas_atas' => 'required|numeric|min:0|max:100',
            'batas_bawah' => 'required|numeric|min:0|max:100',
            'warna_text' => 'required|string|max:7',
            'warna_latar' => 'required|string|max:7',
            'keterangan' => 'nullable|string'
        ]);

        try {
            $predikat->update($request->all());

            return redirect()->route('admin.predikat.index')
                ->with('success', 'Predikat berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui predikat: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $predikat = PredikatKinerja::findOrFail($id);

            // Cek apakah predikat sedang digunakan
            if ($predikat->hasilPenilaian()->count() > 0) {
                return redirect()->route('admin.predikat.index')
                    ->with('error', 'Predikat tidak bisa dihapus karena sudah digunakan!');
            }

            $predikat->delete();

            return redirect()->route('admin.predikat.index')
                ->with('success', 'Predikat berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('admin.predikat.index')
                ->with('error', 'Gagal menghapus predikat: ' . $e->getMessage());
        }
    }
}