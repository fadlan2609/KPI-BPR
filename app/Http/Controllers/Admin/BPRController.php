<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BPR;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BPRController extends Controller
{
    public function index()
    {
        $bpr = BPR::first();
        return view('admin.bpr.index', compact('bpr'));
    }

    public function update(Request $request)
    {
        $bpr = BPR::first();

        if (!$bpr) {
            $bpr = new BPR();
        }

        $request->validate([
            'sandi_bpr' => 'required|string|max:50|unique:bpr,sandi_bpr,' . ($bpr->id ?? ''),
            'nama_bpr' => 'required|string|max:100',
            'jenis_bpr' => 'nullable|string|max:50',
            'jenis_lembaga' => 'nullable|string|max:50',
            'kategori' => 'nullable|string|max:50',
            'no_telp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'email' => 'nullable|email|max:100',
            'website' => 'nullable|url|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048'
        ]);

        $data = $request->except(['logo', '_token', '_method']);

        if ($request->hasFile('logo')) {
            // Hapus logo lama
            if ($bpr && $bpr->logo) {
                Storage::disk('public')->delete($bpr->logo);
            }

            $file = $request->file('logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('logo', $filename, 'public');
            $data['logo'] = $path;
        }

        if ($bpr->exists) {
            $bpr->update($data);
        } else {
            BPR::create($data);
        }

        return redirect()->route('admin.bpr.index')
            ->with('success', 'Informasi BPR berhasil diperbarui!');
    }
}