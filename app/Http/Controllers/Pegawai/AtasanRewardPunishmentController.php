<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\RewardPunishment;
use App\Models\Pegawai;
use App\Notifications\RewardPunishmentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AtasanRewardPunishmentController extends Controller
{
    /**
     * Ambil kategori dari route defaults atau request
     */
    private function getKategori(Request $request)
    {
        return $request->kategori
            ?? $request->route()->defaults['kategori']
            ?? null;
    }

    /**
     * Cek apakah user adalah atasan
     */
    private function checkAtasan()
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) return null;

        if ($pegawai->bawahanLangsung()->count() === 0) return null;

        return $pegawai;
    }

    /**
     * List reward/punishment untuk bawahan
     */
    public function index(Request $request)
    {
        $atasan = $this->checkAtasan();

        if (!$atasan) {
            return redirect()->route('pegawai.dashboard')
                ->with('error', 'Anda tidak memiliki bawahan langsung.');
        }

        $kategori = $this->getKategori($request);
        $bawahanIds = $atasan->bawahanLangsung()->pluck('id')->toArray();

        $query = RewardPunishment::with(['pegawai.jabatan', 'createdBy'])
            ->whereIn('pegawai_id', $bawahanIds);

        if ($kategori) $query->where('kategori', $kategori);
        if ($request->pegawai_id) $query->where('pegawai_id', $request->pegawai_id);
        if ($request->status) $query->where('status', $request->status);

        $data = $query->orderBy('tanggal', 'desc')->paginate(20);

        $bawahan = $atasan->bawahanLangsung()->orderBy('nama')->get();

        $stats = [
            'total_reward' => RewardPunishment::whereIn('pegawai_id', $bawahanIds)->reward()->count(),
            'total_punishment' => RewardPunishment::whereIn('pegawai_id', $bawahanIds)->punishment()->count(),
        ];

        return view('pegawai.atasan-reward-punishment.index', compact(
            'atasan',
            'data',
            'bawahan',
            'stats',
            'kategori'
        ));
    }

    /**
     * Form create
     */
    public function create(Request $request)
    {
        $atasan = $this->checkAtasan();

        if (!$atasan) {
            return redirect()->route('pegawai.dashboard')
                ->with('error', 'Anda tidak memiliki bawahan langsung.');
        }

        $kategori = $this->getKategori($request);
        $bawahan = $atasan->bawahanLangsung()->orderBy('nama')->get();
        $selectedPegawai = $request->pegawai_id;

        return view('pegawai.atasan-reward-punishment.create', compact(
            'atasan',
            'bawahan',
            'selectedPegawai',
            'kategori'
        ));
    }

    /**
     * Store
     */
    public function store(Request $request)
    {
        $atasan = $this->checkAtasan();

        if (!$atasan) {
            return redirect()->route('pegawai.dashboard')
                ->with('error', 'Anda tidak memiliki akses.');
        }

        $bawahanIds = $atasan->bawahanLangsung()->pluck('id')->toArray();

        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'kategori' => 'required|in:reward,punishment',
            'tipe' => 'required|string|max:50',
            'judul' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:2000',
            'tanggal' => 'required|date',
            'nominal' => 'nullable|numeric|min:0',
            'file_bukti' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ]);

        if (!in_array($request->pegawai_id, $bawahanIds)) {
            return back()->with('error', 'Anda hanya bisa input untuk bawahan langsung Anda!')
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $data = $request->except(['file_bukti', '_token']);

            if ($request->hasFile('file_bukti')) {
                $data['file_bukti'] = $request->file('file_bukti')->store('reward-punishment/bukti', 'public');
            }

            $data['created_by'] = $atasan->id;
            $data['status'] = 'aktif';

            $item = RewardPunishment::create($data);

            $pegawaiTarget = Pegawai::find($request->pegawai_id);
            if ($pegawaiTarget) {
                try {
                    $pegawaiTarget->notify(new RewardPunishmentNotification($item, 'created'));
                } catch (\Exception $e) {
                    \Log::warning('Notif gagal: ' . $e->getMessage());
                }
            }

            DB::commit();

            $routeName = $request->kategori === 'reward'
                ? 'pegawai.atasan-reward.index'
                : 'pegawai.atasan-punishment.index';

            return redirect()->route($routeName)
                ->with('success', 'Data berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Hapus (hanya entry sendiri)
     */
    public function destroy($id)
    {
        $atasan = $this->checkAtasan();
        if (!$atasan) return back()->with('error', 'Akses ditolak.');

        $item = RewardPunishment::where('created_by', $atasan->id)->findOrFail($id);
        $kategori = $item->kategori;
        $item->delete();

        $routeName = $kategori === 'reward'
            ? 'pegawai.atasan-reward.index'
            : 'pegawai.atasan-punishment.index';

        return redirect()->route($routeName)
            ->with('success', 'Data berhasil dihapus!');
    }
}