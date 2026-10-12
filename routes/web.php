<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BPRController;
use App\Http\Controllers\Admin\JabatanController;
use App\Http\Controllers\Admin\KantorController;
use App\Http\Controllers\Admin\PegawaiController;
use App\Http\Controllers\Admin\PredikatController;
use App\Http\Controllers\Admin\IndikatorController;
use App\Http\Controllers\Admin\PenilaianController;
use App\Http\Controllers\Admin\PeriodeController;
use App\Http\Controllers\Admin\GajiController;
use App\Http\Controllers\Admin\BonusController;
use App\Http\Controllers\Admin\CutiController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RiwayatJabatanController as AdminRiwayatJabatanController;
use App\Http\Controllers\Admin\RewardPunishmentController as AdminRewardPunishmentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Pegawai\PegawaiDashboardController;
use App\Http\Controllers\Pegawai\SelfAssessmentController;
use App\Http\Controllers\Pegawai\PenilaianBawahanController;
use App\Http\Controllers\Pegawai\CutiPegawaiController;
use App\Http\Controllers\Pegawai\ProfilPegawaiController;
use App\Http\Controllers\Pegawai\RiwayatJabatanController as PegawaiRiwayatJabatanController;
use App\Http\Controllers\Pegawai\RewardPunishmentController as PegawaiRewardPunishmentController;
use App\Http\Controllers\Pegawai\AtasanRewardPunishmentController;

// ==================== AUTH ====================
Route::get('/', fn() => redirect()->route('login'));
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

Route::post('/login', [LoginController::class, 'login'])
    ->middleware('throttle:login')
    ->name('login.post');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ==================== AKSES FILE STORAGE (PENGGANTI SYMLINK) ====================
Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);

    if (!file_exists($fullPath) || is_dir($fullPath)) {
        abort(404, 'File not found');
    }

    $realPath = realpath($fullPath);
    $storagePath = realpath(storage_path('app/public'));

    if ($realPath === false || $storagePath === false) {
        abort(404, 'File not found');
    }

    if (strpos($realPath, $storagePath) !== 0) {
        abort(403, 'Access denied');
    }

    $mime = mime_content_type($realPath);
    if (!$mime) {
        $ext = strtolower(pathinfo($realPath, PATHINFO_EXTENSION));
        $mimeMap = [
            'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png',
            'gif' => 'image/gif', 'webp' => 'image/webp', 'svg' => 'image/svg+xml',
            'pdf' => 'application/pdf',
        ];
        $mime = $mimeMap[$ext] ?? 'application/octet-stream';
    }

    return response()->file($realPath, [
        'Content-Type' => $mime,
        'Cache-Control' => 'public, max-age=604800',
    ]);
})->where('path', '.*')->name('storage.file');

// ==================== ADMIN ====================
Route::middleware(['auth', 'role:admin', 'user.active'])
    ->prefix('admin')->name('admin.')
    ->group(function () {

        // DASHBOARD
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData'])->name('dashboard.chart');

        // PROFILE
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

        // NOTIFIKASI
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::get('/latest', [NotificationController::class, 'latest'])->name('latest');
            Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
            Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read')->where('id', '[0-9]+');
            Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy')->where('id', '[0-9]+');
        });

        // BPR
        Route::get('/bpr', [BPRController::class, 'index'])->name('bpr.index');
        Route::put('/bpr', [BPRController::class, 'update'])->name('bpr.update');
        Route::post('/bpr', [BPRController::class, 'store'])->name('bpr.store');

        // JABATAN
        Route::get('/jabatan', [JabatanController::class, 'index'])->name('jabatan.index');
        Route::get('/jabatan/create', [JabatanController::class, 'create'])->name('jabatan.create');
        Route::post('/jabatan', [JabatanController::class, 'store'])->name('jabatan.store');
        Route::get('/jabatan/{jabatan}/edit', [JabatanController::class, 'edit'])->name('jabatan.edit');
        Route::put('/jabatan/{jabatan}', [JabatanController::class, 'update'])->name('jabatan.update');
        Route::delete('/jabatan/{jabatan}', [JabatanController::class, 'destroy'])->name('jabatan.destroy');
        Route::post('/jabatan/import', [JabatanController::class, 'import'])->name('jabatan.import');
        Route::get('/jabatan/template', [JabatanController::class, 'template'])->name('jabatan.template');

        // KANTOR
        Route::get('/kantor', [KantorController::class, 'index'])->name('kantor.index');
        Route::get('/kantor/create', [KantorController::class, 'create'])->name('kantor.create');
        Route::post('/kantor', [KantorController::class, 'store'])->name('kantor.store');
        Route::get('/kantor/{kantor}/edit', [KantorController::class, 'edit'])->name('kantor.edit');
        Route::put('/kantor/{kantor}', [KantorController::class, 'update'])->name('kantor.update');
        Route::delete('/kantor/{kantor}', [KantorController::class, 'destroy'])->name('kantor.destroy');
        Route::post('/kantor/import', [KantorController::class, 'import'])->name('kantor.import');
        Route::get('/kantor/template', [KantorController::class, 'template'])->name('kantor.template');

        // ==================== PEGAWAI ====================
        Route::get('/pegawai', [PegawaiController::class, 'index'])->name('pegawai.index');
        Route::get('/pegawai/create', [PegawaiController::class, 'create'])->name('pegawai.create');
        Route::post('/pegawai', [PegawaiController::class, 'store'])->name('pegawai.store');
        Route::post('/pegawai/bulk-delete', [PegawaiController::class, 'bulkDestroy'])->name('pegawai.bulk-delete');
        Route::get('/pegawai/{pegawai}/edit', [PegawaiController::class, 'edit'])->name('pegawai.edit');
        Route::put('/pegawai/{pegawai}', [PegawaiController::class, 'update'])->name('pegawai.update');
        Route::delete('/pegawai/{pegawai}', [PegawaiController::class, 'destroy'])->name('pegawai.destroy');
        Route::post('/pegawai/import', [PegawaiController::class, 'import'])->name('pegawai.import');
        Route::get('/pegawai/template', [PegawaiController::class, 'template'])->name('pegawai.template');
        Route::get('/pegawai/export', [PegawaiController::class, 'export'])->name('pegawai.export');
        Route::post('/pegawai/reset-password/{id}', [PegawaiController::class, 'resetPassword'])->name('pegawai.reset-password')->where('id', '[0-9]+');
        Route::post('/pegawai/create-user/{id}', [PegawaiController::class, 'createUser'])->name('pegawai.create-user')->where('id', '[0-9]+');
        Route::delete('/pegawai/delete-user/{id}', [PegawaiController::class, 'deleteUser'])->name('pegawai.delete-user')->where('id', '[0-9]+');

        // ✅ CETAK CV PEGAWAI (ADMIN)
        Route::get('/pegawai/{id}/cetak-cv', [PegawaiController::class, 'cetakCV'])->name('pegawai.cetak-cv')->where('id', '[0-9]+');

        // REWARD (ADMIN)
        Route::prefix('reward')->name('reward.')->group(function () {
            Route::get('/', [AdminRewardPunishmentController::class, 'index'])->name('index')->defaults('kategori', 'reward');
            Route::get('/create', [AdminRewardPunishmentController::class, 'create'])->name('create')->defaults('kategori', 'reward');
            Route::post('/', [AdminRewardPunishmentController::class, 'store'])->name('store');
            Route::get('/laporan/cetak', [AdminRewardPunishmentController::class, 'cetakLaporan'])->name('laporan.cetak')->defaults('kategori', 'reward');
            Route::get('/{id}', [AdminRewardPunishmentController::class, 'show'])->name('show')->where('id', '[0-9]+');
            Route::get('/{id}/edit', [AdminRewardPunishmentController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
            Route::put('/{id}', [AdminRewardPunishmentController::class, 'update'])->name('update')->where('id', '[0-9]+');
            Route::delete('/{id}', [AdminRewardPunishmentController::class, 'destroy'])->name('destroy')->where('id', '[0-9]+');
            Route::patch('/{id}/status', [AdminRewardPunishmentController::class, 'updateStatus'])->name('update-status')->where('id', '[0-9]+');
        });

        // PUNISHMENT (ADMIN)
        Route::prefix('punishment')->name('punishment.')->group(function () {
            Route::get('/', [AdminRewardPunishmentController::class, 'index'])->name('index')->defaults('kategori', 'punishment');
            Route::get('/create', [AdminRewardPunishmentController::class, 'create'])->name('create')->defaults('kategori', 'punishment');
            Route::post('/', [AdminRewardPunishmentController::class, 'store'])->name('store');
            Route::get('/laporan/cetak', [AdminRewardPunishmentController::class, 'cetakLaporan'])->name('laporan.cetak')->defaults('kategori', 'punishment');
            Route::get('/{id}', [AdminRewardPunishmentController::class, 'show'])->name('show')->where('id', '[0-9]+');
            Route::get('/{id}/edit', [AdminRewardPunishmentController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
            Route::put('/{id}', [AdminRewardPunishmentController::class, 'update'])->name('update')->where('id', '[0-9]+');
            Route::delete('/{id}', [AdminRewardPunishmentController::class, 'destroy'])->name('destroy')->where('id', '[0-9]+');
            Route::patch('/{id}/status', [AdminRewardPunishmentController::class, 'updateStatus'])->name('update-status')->where('id', '[0-9]+');
        });

        // RIWAYAT JABATAN (ADMIN)
        Route::prefix('riwayat-jabatan')->name('riwayat-jabatan.')->group(function () {
            Route::get('/', [AdminRiwayatJabatanController::class, 'index'])->name('index');
            Route::get('/permintaan', [AdminRiwayatJabatanController::class, 'permintaan'])->name('permintaan');
            Route::post('/permintaan/{id}/approve', [AdminRiwayatJabatanController::class, 'approvePermintaan'])->name('permintaan.approve')->where('id', '[0-9]+');
            Route::post('/permintaan/{id}/reject', [AdminRiwayatJabatanController::class, 'rejectPermintaan'])->name('permintaan.reject')->where('id', '[0-9]+');
            Route::get('/create', [AdminRiwayatJabatanController::class, 'create'])->name('create');
            Route::post('/', [AdminRiwayatJabatanController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [AdminRiwayatJabatanController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
            Route::get('/{id}/cetak-cv', [AdminRiwayatJabatanController::class, 'cetakCV'])->name('cetak-cv')->where('id', '[0-9]+');
            Route::get('/{id}/download-cv', [AdminRiwayatJabatanController::class, 'downloadCV'])->name('download-cv')->where('id', '[0-9]+');
            Route::put('/{id}', [AdminRiwayatJabatanController::class, 'update'])->name('update')->where('id', '[0-9]+');
            Route::delete('/{id}', [AdminRiwayatJabatanController::class, 'destroy'])->name('destroy')->where('id', '[0-9]+');
            Route::get('/{pegawai}', [AdminRiwayatJabatanController::class, 'show'])->name('show')->where('pegawai', '[0-9]+');
        });

        // PERIODE
        Route::get('/periode', [PeriodeController::class, 'index'])->name('periode.index');
        Route::get('/periode/create', [PeriodeController::class, 'create'])->name('periode.create');
        Route::post('/periode', [PeriodeController::class, 'store'])->name('periode.store');
        Route::get('/periode/{periode}', [PeriodeController::class, 'show'])->name('periode.show')->where('periode', '[0-9]+');
        Route::get('/periode/{periode}/edit', [PeriodeController::class, 'edit'])->name('periode.edit')->where('periode', '[0-9]+');
        Route::put('/periode/{periode}', [PeriodeController::class, 'update'])->name('periode.update')->where('periode', '[0-9]+');
        Route::delete('/periode/{periode}', [PeriodeController::class, 'destroy'])->name('periode.destroy')->where('periode', '[0-9]+');
        Route::get('/periode/activate/{id}', [PeriodeController::class, 'activate'])->name('periode.activate')->where('id', '[0-9]+');
        Route::get('/periode-api/active', [PeriodeController::class, 'getActive'])->name('periode.api.active');
        Route::get('/periode-api/all', [PeriodeController::class, 'getAll'])->name('periode.api.all');
        Route::get('/periode-api/check', [PeriodeController::class, 'checkActive'])->name('periode.api.check');

        // PREDIKAT
        Route::get('/predikat', [PredikatController::class, 'index'])->name('predikat.index');
        Route::get('/predikat/create', [PredikatController::class, 'create'])->name('predikat.create');
        Route::post('/predikat', [PredikatController::class, 'store'])->name('predikat.store');
        Route::get('/predikat/{predikat}/edit', [PredikatController::class, 'edit'])->name('predikat.edit');
        Route::put('/predikat/{predikat}', [PredikatController::class, 'update'])->name('predikat.update');
        Route::delete('/predikat/{predikat}', [PredikatController::class, 'destroy'])->name('predikat.destroy');

        // INDIKATOR
        Route::get('/indikator', [IndikatorController::class, 'index'])->name('indikator.index');
        Route::get('/indikator/jabatan/{jabatan}', [IndikatorController::class, 'edit'])->name('indikator.edit');
        Route::put('/indikator/jabatan/{jabatan}', [IndikatorController::class, 'update'])->name('indikator.update');
        Route::post('/indikator/import', [IndikatorController::class, 'import'])->name('indikator.import');
        Route::get('/indikator/template/{jenis}', [IndikatorController::class, 'template'])->name('indikator.template');
        Route::post('/indikator/import-multi', [IndikatorController::class, 'importMulti'])->name('indikator.import-multi');
        Route::get('/indikator/template-multi', [IndikatorController::class, 'templateMulti'])->name('indikator.template-multi');
        Route::delete('/indikator/{id}', [IndikatorController::class, 'destroy'])->name('indikator.destroy')->where('id', '[0-9]+');

        // PENILAIAN
        Route::prefix('penilaian')->name('penilaian.')->group(function () {
            Route::get('/', [PenilaianController::class, 'index'])->name('index');
            Route::get('/progress', [PenilaianController::class, 'progress'])->name('progress');
            Route::get('/cetak', [PenilaianController::class, 'cetak'])->name('cetak');
            Route::get('/cetak-pdf/{periode}', [PenilaianController::class, 'cetakPDF'])->name('cetak-pdf')->where('periode', '[0-9]+');
            Route::get('/cetak-excel/{periode}', [PenilaianController::class, 'cetakExcel'])->name('cetak-excel')->where('periode', '[0-9]+');
            Route::get('/cetak-single/{pegawai}/{periode}', [PenilaianController::class, 'cetakSingle'])->name('cetak-single')->where('pegawai', '[0-9]+')->where('periode', '[0-9]+');
            Route::get('/preview/{pegawai}/{periode}', [PenilaianController::class, 'preview'])->name('preview')->where('pegawai', '[0-9]+')->where('periode', '[0-9]+');
            Route::get('/preview-all/{periode}', [PenilaianController::class, 'previewAll'])->name('preview-all')->where('periode', '[0-9]+');
            Route::get('/preview-pdf/{pegawai}/{periode}', [PenilaianController::class, 'previewPDF'])->name('preview-pdf')->where('pegawai', '[0-9]+')->where('periode', '[0-9]+');
            Route::get('/preview-all-pdf/{periode}', [PenilaianController::class, 'previewAllPDF'])->name('preview-all-pdf')->where('periode', '[0-9]+');
            Route::post('/finalize/{periode}', [PenilaianController::class, 'finalize'])->name('finalize')->where('periode', '[0-9]+');
            Route::get('/create/{pegawai}/{periode}', [PenilaianController::class, 'create'])->name('create')->where('pegawai', '[0-9]+')->where('periode', '[0-9]+');
            Route::post('/', [PenilaianController::class, 'store'])->name('store');
            Route::get('/{penilaian}', [PenilaianController::class, 'show'])->name('show')->where('penilaian', '[0-9]+');
            Route::delete('/{penilaian}', [PenilaianController::class, 'destroy'])->name('destroy')->where('penilaian', '[0-9]+');
        });

        // GAJI
        Route::prefix('gaji')->name('gaji.')->group(function () {
            Route::get('/', [GajiController::class, 'index'])->name('index');
            Route::get('/kenaikan', [GajiController::class, 'kenaikan'])->name('kenaikan');
            Route::post('/kenaikan/approve', [GajiController::class, 'approveKenaikan'])->name('approve-kenaikan');
            Route::get('/kenaikan/export', [GajiController::class, 'exportKenaikan'])->name('export-kenaikan');
            Route::get('/setting', [GajiController::class, 'setting'])->name('setting');
            Route::post('/setting', [GajiController::class, 'saveSetting'])->name('save-setting');
        });

        // BONUS
        Route::prefix('bonus')->name('bonus.')->group(function () {
            Route::get('/', [BonusController::class, 'index'])->name('index');
            Route::get('/setting/{periode}', [BonusController::class, 'setting'])->name('setting')->where('periode', '[0-9]+');
            Route::post('/setting', [BonusController::class, 'saveSetting'])->name('save-setting');
            Route::post('/hitung', [BonusController::class, 'hitung'])->name('hitung');
            Route::post('/approve', [BonusController::class, 'approve'])->name('approve');
            Route::get('/export', [BonusController::class, 'export'])->name('export');
        });

        // ==================== CUTI (ADMIN) ====================
        Route::prefix('cuti')->name('cuti.')->group(function () {
            // SALDO CUTI — harus di atas /{cuti}
            Route::get('/saldo', [CutiController::class, 'saldo'])->name('saldo');
            Route::get('/saldo/{pegawai}/edit', [CutiController::class, 'editSaldo'])->name('saldo.edit')->where('pegawai', '[0-9]+');
            Route::put('/saldo/{pegawai}', [CutiController::class, 'updateSaldo'])->name('saldo.update')->where('pegawai', '[0-9]+');
            Route::post('/saldo/reset', [CutiController::class, 'resetSaldo'])->name('saldo.reset');

            // UTAMA
            Route::get('/', [CutiController::class, 'index'])->name('index');
            Route::get('/create', [CutiController::class, 'create'])->name('create');
            Route::post('/', [CutiController::class, 'store'])->name('store');
            Route::get('/laporan', [CutiController::class, 'laporan'])->name('laporan');
            Route::get('/laporan/cetak', [CutiController::class, 'cetakLaporan'])->name('laporan.cetak');

            // SURAT
            Route::get('/{id}/cetak-surat', [CutiController::class, 'cetakSurat'])->name('cetak-surat')->where('id', '[0-9]+');
            Route::get('/{id}/download-surat', [CutiController::class, 'downloadSurat'])->name('download-surat')->where('id', '[0-9]+');
            Route::get('/{id}/download-surat-dokter', [CutiController::class, 'downloadSuratDokter'])->name('download-surat-dokter')->where('id', '[0-9]+');
            Route::post('/{id}/approve-surat-dokter', [CutiController::class, 'approveSuratDokter'])->name('approve-surat-dokter')->where('id', '[0-9]+');
            Route::post('/{id}/reject-surat-dokter', [CutiController::class, 'rejectSuratDokter'])->name('reject-surat-dokter')->where('id', '[0-9]+');

            // AKSI
            Route::post('/{id}/cancel', [CutiController::class, 'cancel'])->name('cancel')->where('id', '[0-9]+');
            Route::post('/{id}/approve', [CutiController::class, 'approve'])->name('approve')->where('id', '[0-9]+');
            Route::post('/{id}/reject', [CutiController::class, 'reject'])->name('reject')->where('id', '[0-9]+');

            // CRUD — di bawah
            Route::get('/{cuti}', [CutiController::class, 'show'])->name('show')->where('cuti', '[0-9]+');
            Route::get('/{cuti}/edit', [CutiController::class, 'edit'])->name('edit')->where('cuti', '[0-9]+');
            Route::put('/{cuti}', [CutiController::class, 'update'])->name('update')->where('cuti', '[0-9]+');
            Route::delete('/{cuti}', [CutiController::class, 'destroy'])->name('destroy')->where('cuti', '[0-9]+');
        });

        // LAPORAN
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/', [LaporanController::class, 'index'])->name('index');
            Route::get('/penilaian', [LaporanController::class, 'penilaian'])->name('penilaian');
            Route::get('/penilaian/download', [LaporanController::class, 'downloadPenilaian'])->name('download-penilaian');
            Route::get('/gaji', [LaporanController::class, 'gaji'])->name('gaji');
            Route::get('/gaji/download', [LaporanController::class, 'downloadGaji'])->name('download-gaji');
            Route::get('/bonus', [LaporanController::class, 'bonus'])->name('bonus');
            Route::get('/bonus/download', [LaporanController::class, 'downloadBonus'])->name('download-bonus');
            Route::get('/cuti', [LaporanController::class, 'cuti'])->name('cuti');
            Route::get('/cuti/download', [LaporanController::class, 'downloadCuti'])->name('download-cuti');
        });
    });

// ==================== PEGAWAI ====================
Route::middleware(['auth', 'role:pegawai', 'user.active'])
    ->prefix('pegawai')->name('pegawai.')
    ->group(function () {

        Route::get('/dashboard', [PegawaiDashboardController::class, 'index'])->name('dashboard');

        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::get('/latest', [NotificationController::class, 'latest'])->name('latest');
            Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
            Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read')->where('id', '[0-9]+');
            Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy')->where('id', '[0-9]+');
        });

        // SELF ASSESSMENT
        Route::get('/self-assessment', [SelfAssessmentController::class, 'index'])->name('self-assessment.index');
        Route::post('/self-assessment', [SelfAssessmentController::class, 'store'])->name('self-assessment.store');
        Route::get('/self-assessment/result', [SelfAssessmentController::class, 'result'])->name('self-assessment.result');
        Route::get('/self-assessment/edit', [SelfAssessmentController::class, 'edit'])->name('self-assessment.edit');
        Route::put('/self-assessment', [SelfAssessmentController::class, 'update'])->name('self-assessment.update');
        Route::delete('/self-assessment', [SelfAssessmentController::class, 'destroy'])->name('self-assessment.destroy');
        Route::get('/self-assessment/print', [SelfAssessmentController::class, 'print'])->name('self-assessment.print');

        // PENILAIAN BAWAHAN
        Route::get('/penilaian-bawahan', [PenilaianBawahanController::class, 'index'])->name('penilaian-bawahan.index');
        Route::get('/penilaian-bawahan/create/{pegawai}', [PenilaianBawahanController::class, 'create'])->name('penilaian-bawahan.create')->where('pegawai', '[0-9]+');
        Route::post('/penilaian-bawahan', [PenilaianBawahanController::class, 'store'])->name('penilaian-bawahan.store');
        Route::get('/penilaian-bawahan/{pegawai}', [PenilaianBawahanController::class, 'show'])->name('penilaian-bawahan.show')->where('pegawai', '[0-9]+');

        // HASIL PENILAIAN
        Route::get('/hasil-penilaian', [PegawaiDashboardController::class, 'hasilPenilaian'])->name('hasil-penilaian');
        Route::get('/cetak-laporan/{hasil_id}', [PegawaiDashboardController::class, 'cetakLaporan'])->name('cetak-laporan')->where('hasil_id', '[0-9]+');
        Route::get('/cetak-laporan-pdf/{hasil_id}', [PegawaiDashboardController::class, 'cetakLaporanPDF'])->name('cetak-laporan-pdf')->where('hasil_id', '[0-9]+');

        // REWARD (PEGAWAI)
        Route::prefix('reward')->name('reward.')->group(function () {
            Route::get('/', [PegawaiRewardPunishmentController::class, 'index'])->name('index')->defaults('kategori', 'reward');
            Route::get('/cetak-laporan', [PegawaiRewardPunishmentController::class, 'cetakLaporan'])->name('cetak-laporan')->defaults('kategori', 'reward');
            Route::get('/{id}', [PegawaiRewardPunishmentController::class, 'show'])->name('show')->where('id', '[0-9]+');
        });

        // PUNISHMENT (PEGAWAI)
        Route::prefix('punishment')->name('punishment.')->group(function () {
            Route::get('/', [PegawaiRewardPunishmentController::class, 'index'])->name('index')->defaults('kategori', 'punishment');
            Route::get('/cetak-laporan', [PegawaiRewardPunishmentController::class, 'cetakLaporan'])->name('cetak-laporan')->defaults('kategori', 'punishment');
            Route::get('/{id}', [PegawaiRewardPunishmentController::class, 'show'])->name('show')->where('id', '[0-9]+');
        });

        // ATASAN INPUT REWARD
        Route::prefix('atasan-reward')->name('atasan-reward.')->group(function () {
            Route::get('/', [AtasanRewardPunishmentController::class, 'index'])->name('index')->defaults('kategori', 'reward');
            Route::get('/create', [AtasanRewardPunishmentController::class, 'create'])->name('create')->defaults('kategori', 'reward');
            Route::post('/', [AtasanRewardPunishmentController::class, 'store'])->name('store');
            Route::delete('/{id}', [AtasanRewardPunishmentController::class, 'destroy'])->name('destroy')->where('id', '[0-9]+');
        });

        // ATASAN INPUT PUNISHMENT
        Route::prefix('atasan-punishment')->name('atasan-punishment.')->group(function () {
            Route::get('/', [AtasanRewardPunishmentController::class, 'index'])->name('index')->defaults('kategori', 'punishment');
            Route::get('/create', [AtasanRewardPunishmentController::class, 'create'])->name('create')->defaults('kategori', 'punishment');
            Route::post('/', [AtasanRewardPunishmentController::class, 'store'])->name('store');
            Route::delete('/{id}', [AtasanRewardPunishmentController::class, 'destroy'])->name('destroy')->where('id', '[0-9]+');
        });

        // RIWAYAT JABATAN
        Route::prefix('riwayat-jabatan')->name('riwayat-jabatan.')->group(function () {
            Route::get('/', [PegawaiRiwayatJabatanController::class, 'index'])->name('index');
            Route::post('/request-edit', [PegawaiRiwayatJabatanController::class, 'requestEdit'])->name('request-edit');
            Route::get('/cetak-cv', [PegawaiRiwayatJabatanController::class, 'cetakCV'])->name('cetak-cv');
            Route::get('/download-cv', [PegawaiRiwayatJabatanController::class, 'downloadCV'])->name('download-cv');
            Route::get('/create', [PegawaiRiwayatJabatanController::class, 'create'])->name('create');
            Route::post('/', [PegawaiRiwayatJabatanController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [PegawaiRiwayatJabatanController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
            Route::put('/{id}', [PegawaiRiwayatJabatanController::class, 'update'])->name('update')->where('id', '[0-9]+');
        });

        // CUTI PEGAWAI
        Route::prefix('cuti')->name('cuti.')->group(function () {
            // APPROVAL — di atas /{id}
            Route::get('/approval', [CutiPegawaiController::class, 'approvalIndex'])->name('approval.index');
            Route::get('/approval/{id}', [CutiPegawaiController::class, 'approvalShow'])->name('approval.show')->where('id', '[0-9]+');
            Route::post('/approval/{id}/approve', [CutiPegawaiController::class, 'approve'])->name('approval.approve')->where('id', '[0-9]+');
            Route::post('/approval/{id}/reject', [CutiPegawaiController::class, 'reject'])->name('approval.reject')->where('id', '[0-9]+');

            // UTAMA
            Route::get('/', [CutiPegawaiController::class, 'index'])->name('index');
            Route::post('/', [CutiPegawaiController::class, 'store'])->name('store');
            Route::post('/calculate-date', [CutiPegawaiController::class, 'calculateDate'])->name('calculate-date');

            // DETAIL — di bawah
            Route::get('/{id}/upload-surat-dokter', [CutiPegawaiController::class, 'uploadSuratDokterForm'])->name('upload-surat.form')->where('id', '[0-9]+');
            Route::post('/{id}/upload-surat-dokter', [CutiPegawaiController::class, 'uploadSuratDokter'])->name('upload-surat')->where('id', '[0-9]+');
            Route::post('/{id}/cancel', [CutiPegawaiController::class, 'cancel'])->name('cancel')->where('id', '[0-9]+');
        });

        Route::get('/cuti/saldo', [PegawaiDashboardController::class, 'saldoCuti'])->name('cuti.saldo');

        // ==================== PROFIL PEGAWAI ====================
        Route::prefix('profil')->name('profil.')->group(function () {
            Route::get('/', [ProfilPegawaiController::class, 'index'])->name('index');
            Route::put('/', [ProfilPegawaiController::class, 'update'])->name('update');
            Route::put('/password', [ProfilPegawaiController::class, 'updatePassword'])->name('update-password');

            // ✅ CETAK CV PEGAWAI (untuk dirinya sendiri)
            Route::get('/cetak-cv', [ProfilPegawaiController::class, 'cetakCV'])->name('cetak-cv');
        });

        // API
        Route::get('/api/data', [PegawaiDashboardController::class, 'getData'])->name('api.data');
        Route::get('/api/progress', [PegawaiDashboardController::class, 'getProgress'])->name('api.progress');
        Route::get('/api/hasil', [PegawaiDashboardController::class, 'getHasilPenilaian'])->name('api.hasil');
        Route::get('/api/cuti', [PegawaiDashboardController::class, 'getCuti'])->name('api.cuti');
    });

// ==================== FALLBACK ====================
Route::fallback(function () {
    return redirect()->route('login');
});