<?php

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
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Pegawai\PegawaiDashboardController;
use App\Http\Controllers\Pegawai\SelfAssessmentController;
use App\Http\Controllers\Pegawai\PenilaianBawahanController;
use Illuminate\Support\Facades\Route;

// ==================== AUTHENTICATION ====================
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ==================== ADMIN ROUTES ====================
Route::middleware(['auth', 'role:admin', 'user.active'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        
        // ==================== DASHBOARD ====================
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData'])->name('dashboard.chart');
        
        // ==================== INFORMASI BPR ====================
        // BPR / Identitas BPR
        Route::get('/bpr', [BPRController::class, 'index'])->name('bpr.index');
        Route::put('/bpr', [BPRController::class, 'update'])->name('bpr.update');
        Route::post('/bpr', [BPRController::class, 'store'])->name('bpr.store');
        
        // Jabatan
        Route::get('/jabatan', [JabatanController::class, 'index'])->name('jabatan.index');
        Route::get('/jabatan/create', [JabatanController::class, 'create'])->name('jabatan.create');
        Route::post('/jabatan', [JabatanController::class, 'store'])->name('jabatan.store');
        Route::get('/jabatan/{jabatan}/edit', [JabatanController::class, 'edit'])->name('jabatan.edit');
        Route::put('/jabatan/{jabatan}', [JabatanController::class, 'update'])->name('jabatan.update');
        Route::delete('/jabatan/{jabatan}', [JabatanController::class, 'destroy'])->name('jabatan.destroy');
        Route::post('/jabatan/import', [JabatanController::class, 'import'])->name('jabatan.import');
        Route::get('/jabatan/template', [JabatanController::class, 'template'])->name('jabatan.template');
        
        // Kantor
        Route::get('/kantor', [KantorController::class, 'index'])->name('kantor.index');
        Route::get('/kantor/create', [KantorController::class, 'create'])->name('kantor.create');
        Route::post('/kantor', [KantorController::class, 'store'])->name('kantor.store');
        Route::get('/kantor/{kantor}/edit', [KantorController::class, 'edit'])->name('kantor.edit');
        Route::put('/kantor/{kantor}', [KantorController::class, 'update'])->name('kantor.update');
        Route::delete('/kantor/{kantor}', [KantorController::class, 'destroy'])->name('kantor.destroy');
        Route::post('/kantor/import', [KantorController::class, 'import'])->name('kantor.import');
        Route::get('/kantor/template', [KantorController::class, 'template'])->name('kantor.template');
        
        // Pegawai
        Route::get('/pegawai', [PegawaiController::class, 'index'])->name('pegawai.index');
        Route::get('/pegawai/create', [PegawaiController::class, 'create'])->name('pegawai.create');
        Route::post('/pegawai', [PegawaiController::class, 'store'])->name('pegawai.store');
        Route::get('/pegawai/{pegawai}/edit', [PegawaiController::class, 'edit'])->name('pegawai.edit');
        Route::put('/pegawai/{pegawai}', [PegawaiController::class, 'update'])->name('pegawai.update');
        Route::delete('/pegawai/{pegawai}', [PegawaiController::class, 'destroy'])->name('pegawai.destroy');
        Route::post('/pegawai/import', [PegawaiController::class, 'import'])->name('pegawai.import');
        Route::get('/pegawai/template', [PegawaiController::class, 'template'])->name('pegawai.template');
        Route::post('/pegawai/reset-password/{id}', [PegawaiController::class, 'resetPassword'])->name('pegawai.reset-password');
        Route::post('/pegawai/create-user/{id}', [PegawaiController::class, 'createUser'])->name('pegawai.create-user');
        
        // ==================== KPI & PENILAIAN ====================
        // Periode Penilaian
        Route::get('/periode', [PeriodeController::class, 'index'])->name('periode.index');
        Route::get('/periode/create', [PeriodeController::class, 'create'])->name('periode.create');
        Route::post('/periode', [PeriodeController::class, 'store'])->name('periode.store');
        Route::get('/periode/{periode}', [PeriodeController::class, 'show'])->name('periode.show');
        Route::get('/periode/{periode}/edit', [PeriodeController::class, 'edit'])->name('periode.edit');
        Route::put('/periode/{periode}', [PeriodeController::class, 'update'])->name('periode.update');
        Route::delete('/periode/{periode}', [PeriodeController::class, 'destroy'])->name('periode.destroy');
        Route::get('/periode/activate/{id}', [PeriodeController::class, 'activate'])->name('periode.activate');
        
        // API Routes untuk Periode (AJAX)
        Route::get('/periode-api/active', [PeriodeController::class, 'getActive'])->name('periode.api.active');
        Route::get('/periode-api/all', [PeriodeController::class, 'getAll'])->name('periode.api.all');
        Route::get('/periode-api/check', [PeriodeController::class, 'checkActive'])->name('periode.api.check');
        
        // Predikat Kinerja
        Route::get('/predikat', [PredikatController::class, 'index'])->name('predikat.index');
        Route::get('/predikat/create', [PredikatController::class, 'create'])->name('predikat.create');
        Route::post('/predikat', [PredikatController::class, 'store'])->name('predikat.store');
        Route::get('/predikat/{predikat}/edit', [PredikatController::class, 'edit'])->name('predikat.edit');
        Route::put('/predikat/{predikat}', [PredikatController::class, 'update'])->name('predikat.update');
        Route::delete('/predikat/{predikat}', [PredikatController::class, 'destroy'])->name('predikat.destroy');
        
        // Indikator Penilaian
        Route::get('/indikator', [IndikatorController::class, 'index'])->name('indikator.index');
        Route::get('/indikator/jabatan/{jabatan}', [IndikatorController::class, 'edit'])->name('indikator.edit');
        Route::put('/indikator/jabatan/{jabatan}', [IndikatorController::class, 'update'])->name('indikator.update');
        Route::post('/indikator/import', [IndikatorController::class, 'import'])->name('indikator.import');
        Route::get('/indikator/template/{jenis}', [IndikatorController::class, 'template'])->name('indikator.template');
        Route::post('/indikator/import-multi', [IndikatorController::class, 'importMulti'])->name('indikator.import-multi');
        Route::get('/indikator/template-multi', [IndikatorController::class, 'templateMulti'])->name('indikator.template-multi');
        Route::delete('/indikator/{id}', [IndikatorController::class, 'destroy'])->name('indikator.destroy');
        
        // ==================== PENILAIAN KPI ====================
        Route::get('/penilaian', [PenilaianController::class, 'index'])->name('penilaian.index');
        Route::get('/penilaian/progress', [PenilaianController::class, 'progress'])->name('penilaian.progress');
        Route::get('/penilaian/create/{pegawai}/{periode}', [PenilaianController::class, 'create'])->name('penilaian.create');
        Route::post('/penilaian', [PenilaianController::class, 'store'])->name('penilaian.store');
        Route::get('/penilaian/{penilaian}', [PenilaianController::class, 'show'])->name('penilaian.show');
        Route::post('/penilaian/finalize/{periode}', [PenilaianController::class, 'finalize'])->name('penilaian.finalize');
        
        // ===== CETAK HASIL PENILAIAN =====
        Route::get('/penilaian-cetak', [PenilaianController::class, 'cetak'])->name('penilaian.cetak');
        Route::get('/penilaian-cetak-pdf/{periode}', [PenilaianController::class, 'cetakPDF'])->name('penilaian.cetak-pdf');
        Route::get('/penilaian-cetak-excel/{periode}', [PenilaianController::class, 'cetakExcel'])->name('penilaian.cetak-excel');
        Route::get('/penilaian-cetak-single/{pegawai}/{periode}', [PenilaianController::class, 'cetakSingle'])->name('penilaian.cetak-single');
        
        // ===== PREVIEW PDF =====
        Route::get('/penilaian-preview/{pegawai}/{periode}', [PenilaianController::class, 'previewPDF'])->name('penilaian.preview');
        Route::get('/penilaian-preview-all/{periode}', [PenilaianController::class, 'previewAllPDF'])->name('penilaian.preview-all');
        
        // ==================== GAJI ====================
        Route::prefix('gaji')->name('gaji.')->group(function () {
            Route::get('/', [GajiController::class, 'index'])->name('index');
            Route::get('/kenaikan', [GajiController::class, 'kenaikan'])->name('kenaikan');
            Route::post('/kenaikan/approve', [GajiController::class, 'approveKenaikan'])->name('approve-kenaikan');
            Route::get('/kenaikan/export', [GajiController::class, 'exportKenaikan'])->name('export-kenaikan');
            Route::get('/setting', [GajiController::class, 'setting'])->name('setting');
            Route::post('/setting', [GajiController::class, 'saveSetting'])->name('save-setting');
        });
        
        // ==================== BONUS ====================
        Route::prefix('bonus')->name('bonus.')->group(function () {
            Route::get('/', [BonusController::class, 'index'])->name('index');
            Route::get('/setting/{periode}', [BonusController::class, 'setting'])->name('setting');
            Route::post('/setting', [BonusController::class, 'saveSetting'])->name('save-setting');
            Route::post('/hitung', [BonusController::class, 'hitung'])->name('hitung');
            Route::post('/approve', [BonusController::class, 'approve'])->name('approve');
            Route::get('/export', [BonusController::class, 'export'])->name('export');
        });
        
        // ==================== CUTI ====================
        Route::prefix('cuti')->name('cuti.')->group(function () {
            // Resource routes
            Route::get('/', [CutiController::class, 'index'])->name('index');
            Route::get('/create', [CutiController::class, 'create'])->name('create');
            Route::post('/', [CutiController::class, 'store'])->name('store');
            Route::get('/{cuti}', [CutiController::class, 'show'])->name('show');
            Route::get('/{cuti}/edit', [CutiController::class, 'edit'])->name('edit');
            Route::put('/{cuti}', [CutiController::class, 'update'])->name('update');
            Route::delete('/{cuti}', [CutiController::class, 'destroy'])->name('destroy');
            
            // Custom routes
            Route::get('/approve/{id}', [CutiController::class, 'approve'])->name('approve');
            Route::get('/reject/{id}', [CutiController::class, 'reject'])->name('reject');
            Route::get('/reopen/{id}', [CutiController::class, 'reopen'])->name('reopen');
            Route::get('/cancel-approval/{id}', [CutiController::class, 'cancelApproval'])->name('cancel-approval');
            Route::get('/saldo/{pegawai}', [CutiController::class, 'saldo'])->name('saldo');
            Route::get('/saldo-json/{pegawaiId}', [CutiController::class, 'getSaldo'])->name('saldo.json');
            Route::post('/calculate-date-range', [CutiController::class, 'calculateDateRange'])->name('calculate-date-range');
        });
        
        // ==================== LAPORAN ====================
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/', [LaporanController::class, 'index'])->name('index');
            Route::get('/penilaian', [LaporanController::class, 'penilaian'])->name('penilaian');
            Route::get('/penilaian/download', [LaporanController::class, 'downloadPenilaian'])->name('download-penilaian');
            Route::get('/gaji', [LaporanController::class, 'gaji'])->name('gaji');
            Route::get('/bonus', [LaporanController::class, 'bonus'])->name('bonus');
            Route::get('/cuti', [LaporanController::class, 'cuti'])->name('cuti');
        });
    });

// ==================== PEGAWAI ROUTES ====================
Route::middleware(['auth', 'role:pegawai', 'user.active'])
    ->prefix('pegawai')
    ->name('pegawai.')
    ->group(function () {
        
        // ===== DASHBOARD =====
        Route::get('/dashboard', [PegawaiDashboardController::class, 'index'])->name('dashboard');
        
        // ===== SELF ASSESSMENT =====
        Route::get('/self-assessment', [SelfAssessmentController::class, 'index'])->name('self-assessment.index');
        Route::post('/self-assessment', [SelfAssessmentController::class, 'store'])->name('self-assessment.store');
        Route::get('/self-assessment/result', [SelfAssessmentController::class, 'result'])->name('self-assessment.result');
        Route::get('/self-assessment/edit', [SelfAssessmentController::class, 'edit'])->name('self-assessment.edit');
        Route::put('/self-assessment', [SelfAssessmentController::class, 'update'])->name('self-assessment.update');
        Route::delete('/self-assessment', [SelfAssessmentController::class, 'destroy'])->name('self-assessment.destroy');
        Route::get('/self-assessment/print', [SelfAssessmentController::class, 'print'])->name('self-assessment.print');
        
        // ===== PENILAIAN BAWAHAN (UNTUK ATASAN) =====
        Route::get('/penilaian-bawahan', [PenilaianBawahanController::class, 'index'])->name('penilaian-bawahan.index');
        Route::get('/penilaian-bawahan/create/{pegawai}', [PenilaianBawahanController::class, 'create'])->name('penilaian-bawahan.create');
        Route::post('/penilaian-bawahan', [PenilaianBawahanController::class, 'store'])->name('penilaian-bawahan.store');
        Route::get('/penilaian-bawahan/{pegawai}', [PenilaianBawahanController::class, 'show'])->name('penilaian-bawahan.show');
        
        // ===== HASIL PENILAIAN =====
        Route::get('/hasil-penilaian', [PegawaiDashboardController::class, 'hasilPenilaian'])->name('hasil-penilaian');
        
        // ===== CETAK LAPORAN LENGKAP =====
        Route::get('/cetak-laporan/{hasil_id}', [PegawaiDashboardController::class, 'cetakLaporan'])->name('cetak-laporan');
        Route::get('/cetak-laporan-pdf/{hasil_id}', [PegawaiDashboardController::class, 'cetakLaporanPDF'])->name('cetak-laporan-pdf');
        
        // ===== CUTI =====
        Route::get('/cuti', [PegawaiDashboardController::class, 'cuti'])->name('cuti');
        Route::post('/cuti', [PegawaiDashboardController::class, 'storeCuti'])->name('cuti.store');
        Route::get('/cuti/saldo', [PegawaiDashboardController::class, 'saldoCuti'])->name('cuti.saldo');
        
        // ===== API / AJAX =====
        Route::get('/api/data', [PegawaiDashboardController::class, 'getData'])->name('api.data');
        Route::get('/api/progress', [PegawaiDashboardController::class, 'getProgress'])->name('api.progress');
        Route::get('/api/hasil', [PegawaiDashboardController::class, 'getHasilPenilaian'])->name('api.hasil');
        Route::get('/api/cuti', [PegawaiDashboardController::class, 'getCuti'])->name('api.cuti');
    });

// ==================== FALLBACK ====================
Route::fallback(function () {
    return redirect()->route('login');
});