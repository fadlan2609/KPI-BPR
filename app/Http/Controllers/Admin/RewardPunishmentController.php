<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RewardPunishment;
use App\Models\Pegawai;
use App\Notifications\RewardPunishmentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class RewardPunishmentController extends Controller
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
     * List semua reward / punishment
     */
    public function index(Request $request)
    {
        $kategori = $this->getKategori($request);

        $query = RewardPunishment::with(['pegawai.jabatan', 'createdBy']);

        if ($kategori) $query->where('kategori', $kategori);
        if ($request->tipe) $query->where('tipe', $request->tipe);
        if ($request->status) $query->where('status', $request->status);
        if ($request->pegawai_id) $query->where('pegawai_id', $request->pegawai_id);
        if ($request->dari_tanggal && $request->sampai_tanggal) {
            $query->whereBetween('tanggal', [$request->dari_tanggal, $request->sampai_tanggal]);
        }
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->search . '%')
                  ->orWhere('keterangan', 'like', '%' . $request->search . '%')
                  ->orWhereHas('pegawai', function ($pq) use ($request) {
                      $pq->where('nama', 'like', '%' . $request->search . '%')
                         ->orWhere('nip', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $data = $query->orderBy('tanggal', 'desc')->paginate(20);

        $pegawai = Pegawai::where('status', 'aktif')->orderBy('nama')->get();

        $stats = [
            'total_reward' => RewardPunishment::reward()->count(),
            'total_punishment' => RewardPunishment::punishment()->count(),
            'total_nominal_reward' => RewardPunishment::reward()->where('status', 'aktif')->sum('nominal'),
            'total_nominal_punishment' => RewardPunishment::punishment()->where('status', 'aktif')->sum('nominal'),
        ];

        return view('admin.reward-punishment.index', compact('data', 'pegawai', 'stats', 'kategori'));
    }

    /**
     * Form create
     */
    public function create(Request $request)
    {
        $kategori = $this->getKategori($request);

        $pegawai = Pegawai::where('status', 'aktif')->orderBy('nama')->get();
        $selectedPegawai = $request->pegawai_id;

        return view('admin.reward-punishment.create', compact('pegawai', 'selectedPegawai', 'kategori'));
    }

    /**
     * Store
     */
    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'kategori' => 'required|in:reward,punishment',
            'tipe' => 'required|string|max:50',
            'judul' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:2000',
            'tanggal' => 'required|date',
            'nominal' => 'nullable|numeric|min:0',
            'file_bukti' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
            'status' => 'required|in:aktif,selesai,dibatalkan',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->except(['file_bukti', '_token']);

            if ($request->hasFile('file_bukti')) {
                $data['file_bukti'] = $request->file('file_bukti')->store('reward-punishment/bukti', 'public');
            }

            $user = Auth::user();
            if ($user->pegawai) {
                $data['created_by'] = $user->pegawai->id;
            }

            $item = RewardPunishment::create($data);

            $pegawaiTarget = Pegawai::find($request->pegawai_id);
            if ($pegawaiTarget) {
                try {
                    $pegawaiTarget->notify(new RewardPunishmentNotification($item, 'created'));
                } catch (\Exception $e) {
                    \Log::warning('Notif reward/punishment gagal: ' . $e->getMessage());
                }
            }

            DB::commit();

            $routeName = $request->kategori === 'reward'
                ? 'admin.reward.index'
                : 'admin.punishment.index';

            return redirect()->route($routeName)
                ->with('success', 'Data berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show detail
     */
    public function show($id)
    {
        $item = RewardPunishment::with(['pegawai.jabatan', 'pegawai.kantor', 'createdBy', 'updatedBy'])
            ->findOrFail($id);

        $activities = \Spatie\Activitylog\Models\Activity::where('subject_type', RewardPunishment::class)
            ->where('subject_id', $id)
            ->with('causer')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.reward-punishment.show', compact('item', 'activities'));
    }

    /**
     * Form edit
     */
    public function edit($id)
    {
        $item = RewardPunishment::findOrFail($id);
        $pegawai = Pegawai::where('status', 'aktif')->orderBy('nama')->get();

        return view('admin.reward-punishment.edit', compact('item', 'pegawai'));
    }

    /**
     * Update
     */
    public function update(Request $request, $id)
    {
        $item = RewardPunishment::findOrFail($id);

        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'kategori' => 'required|in:reward,punishment',
            'tipe' => 'required|string|max:50',
            'judul' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:2000',
            'tanggal' => 'required|date',
            'nominal' => 'nullable|numeric|min:0',
            'file_bukti' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
            'status' => 'required|in:aktif,selesai,dibatalkan',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->except(['file_bukti', '_token', '_method']);

            if ($request->hasFile('file_bukti')) {
                if ($item->file_bukti && Storage::disk('public')->exists($item->file_bukti)) {
                    Storage::disk('public')->delete($item->file_bukti);
                }
                $data['file_bukti'] = $request->file('file_bukti')->store('reward-punishment/bukti', 'public');
            }

            $user = Auth::user();
            if ($user->pegawai) {
                $data['updated_by'] = $user->pegawai->id;
            }

            $oldStatus = $item->status;
            $item->update($data);

            if ($oldStatus !== $request->status) {
                $pegawaiTarget = Pegawai::find($request->pegawai_id);
                if ($pegawaiTarget) {
                    try {
                        $pegawaiTarget->notify(new RewardPunishmentNotification($item, 'status_changed'));
                    } catch (\Exception $e) {
                        \Log::warning('Notif status change gagal: ' . $e->getMessage());
                    }
                }
            }

            DB::commit();

            $routeName = $request->kategori === 'reward'
                ? 'admin.reward.index'
                : 'admin.punishment.index';

            return redirect()->route($routeName)
                ->with('success', 'Data berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Hapus (soft delete)
     */
    public function destroy($id)
    {
        $item = RewardPunishment::findOrFail($id);
        $kategori = $item->kategori;
        $item->delete();

        $routeName = $kategori === 'reward'
            ? 'admin.reward.index'
            : 'admin.punishment.index';

        return redirect()->route($routeName)
            ->with('success', 'Data berhasil dihapus!');
    }

    /**
     * Update status only
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:aktif,selesai,dibatalkan',
        ]);

        $item = RewardPunishment::findOrFail($id);
        $oldStatus = $item->status;

        $item->update([
            'status' => $request->status,
            'updated_by' => Auth::user()->pegawai->id ?? null,
        ]);

        if ($oldStatus !== $request->status) {
            $pegawaiTarget = Pegawai::find($item->pegawai_id);
            if ($pegawaiTarget) {
                try {
                    $pegawaiTarget->notify(new RewardPunishmentNotification($item, 'status_changed'));
                } catch (\Exception $e) {
                    \Log::warning('Notif gagal: ' . $e->getMessage());
                }
            }
        }

        return back()->with('success', 'Status berhasil diubah!');
    }

    /**
     * Cetak laporan per periode
     */
    public function cetakLaporan(Request $request)
    {
        $kategori = $this->getKategori($request);

        $query = RewardPunishment::with(['pegawai.jabatan', 'pegawai.kantor']);

        if ($kategori) $query->where('kategori', $kategori);
        if ($request->status) $query->where('status', $request->status);
        if ($request->pegawai_id) $query->where('pegawai_id', $request->pegawai_id);
        if ($request->dari_tanggal && $request->sampai_tanggal) {
            $query->whereBetween('tanggal', [$request->dari_tanggal, $request->sampai_tanggal]);
        }

        $data = $query->orderBy('tanggal', 'asc')->get();
        $bpr = \App\Models\BPR::first();

        $periodeLabel = 'Semua Data';
        if ($request->dari_tanggal && $request->sampai_tanggal) {
            $periodeLabel = \Carbon\Carbon::parse($request->dari_tanggal)->translatedFormat('d F Y')
                . ' s/d ' . \Carbon\Carbon::parse($request->sampai_tanggal)->translatedFormat('d F Y');
        }

        $pdf = Pdf::loadView('admin.reward-punishment.laporan-periode-pdf', compact('data', 'bpr', 'periodeLabel', 'request', 'kategori'));
        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('defaultFont', 'Times-Roman');

        $namaFile = 'Laporan_' . ucfirst($kategori ?? 'Reward_Punishment') . '_' . date('Ymd') . '.pdf';

        return $pdf->stream($namaFile);
    }
}