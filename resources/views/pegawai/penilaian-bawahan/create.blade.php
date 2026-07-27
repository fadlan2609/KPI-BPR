@extends('layouts.pegawai')

@section('title', 'Penilaian Bawahan')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Penilaian Bawahan</h1>
            <p class="text-sm text-gray-500">
                Pegawai: {{ $pegawai->nama }} ({{ $pegawai->nip }}) | 
                Jabatan: {{ $pegawai->jabatan->nama ?? '-' }}
            </p>
            <p class="text-xs text-gray-400 mt-1">
                Anda menilai sebagai: <strong>{{ $levelPenilai == 'atasan_langsung' ? 'Atasan Langsung' : 'Atasan Penilai' }}</strong>
            </p>
        </div>
        <a href="{{ route('pegawai.penilaian-bawahan.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md text-sm">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <!-- Info Self Assessment -->
    @if($selfAssessment)
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <p class="text-sm text-green-800">
                <i class="fas fa-check-circle mr-2"></i>
                Pegawai telah menyelesaikan Self Assessment pada {{ \Carbon\Carbon::parse($selfAssessment->submitted_at)->format('d M Y H:i') }}
                dengan nilai total: <strong>{{ $selfAssessment->nilai_total }}</strong>
            </p>
            <p class="text-xs text-green-600 mt-1">
                <i class="fas fa-info-circle mr-1"></i>
                Gunakan nilai Self Assessment sebagai referensi untuk penilaian Anda.
            </p>
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-sm text-yellow-800">
                <i class="fas fa-clock mr-2"></i>
                Pegawai belum mengisi Self Assessment. Namun Anda tetap bisa memberikan penilaian.
            </p>
        </div>
    @endif

    <!-- ==================== LIVE SCORE ==================== -->
    <div class="bg-white rounded-lg shadow p-6 border-2 border-blue-200">
        <h3 class="text-lg font-semibold mb-4">
            <i class="fas fa-calculator text-blue-600 mr-2"></i>
            Hasil Perhitungan LIVE
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="p-3 bg-blue-50 rounded-lg text-center">
                <p class="text-sm text-gray-600">Nilai KPI</p>
                <p class="text-2xl font-bold text-blue-600" id="live-kpi">0</p>
                <p class="text-xs text-gray-500">× {{ $bobot->bobot_kpi ?? 70 }}%</p>
            </div>
            <div class="p-3 bg-green-50 rounded-lg text-center">
                <p class="text-sm text-gray-600">Nilai Kompetensi</p>
                <p class="text-2xl font-bold text-green-600" id="live-kompetensi">0</p>
                <p class="text-xs text-gray-500">× {{ $bobot->bobot_kompetensi ?? 15 }}%</p>
            </div>
            <div class="p-3 bg-yellow-50 rounded-lg text-center">
                <p class="text-sm text-gray-600">Nilai Core Values</p>
                <p class="text-2xl font-bold text-yellow-600" id="live-core">0</p>
                <p class="text-xs text-gray-500">× {{ $bobot->bobot_core_values ?? 15 }}%</p>
            </div>
            <div class="p-3 bg-purple-50 rounded-lg border-2 border-purple-300 text-center">
                <p class="text-sm text-gray-600">NILAI TOTAL</p>
                <p class="text-3xl font-bold text-purple-700" id="live-total">0</p>
            </div>
        </div>
        <div class="mt-3 text-center">
            <span id="predikat-live" class="px-4 py-2 rounded-full text-sm font-semibold inline-block">-</span>
        </div>
    </div>

    <form action="{{ route('pegawai.penilaian-bawahan.store') }}" method="POST" id="formPenilaian">
        @csrf
        <input type="hidden" name="pegawai_id" value="{{ $pegawai->id }}">
        <input type="hidden" name="level_penilai" value="{{ $levelPenilai ?? 'atasan_langsung' }}">

        <!-- ==================== KPI ==================== -->
        <div class="bg-white rounded-lg shadow p-6 mt-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">
                    <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                    KPI (Bobot: {{ $bobot->bobot_kpi ?? 70 }}%)
                </h3>
                <span class="text-xs text-gray-500">Skala 0-100</span>
            </div>
            @if($indikator['kpi'] && $indikator['kpi']->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">No</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Indikator</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Target</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Bobot</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Nilai (0-100)</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Komentar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($indikator['kpi'] as $index => $ind)
                            <tr>
                                <td class="px-4 py-2 text-sm">{{ $index + 1 }}</td>
                                <td class="px-4 py-2 text-sm">{{ $ind->indikator }}</td>
                                <td class="px-4 py-2 text-sm">{{ $ind->target }} {{ $ind->satuan }}</td>
                                <td class="px-4 py-2 text-sm">{{ $ind->bobot }}%</td>
                                <td class="px-4 py-2">
                                    <input type="number" name="nilai_kpi[]" required
                                           min="0" max="100" step="0.5"
                                           class="w-24 px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 nilai-input"
                                           data-jenis="kpi">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="text" name="komentar_kpi[]" 
                                           class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           placeholder="Opsional">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50">
                                <td colspan="4" class="px-4 py-2 text-right font-medium">Rata-rata Nilai KPI:</td>
                                <td class="px-4 py-2">
                                    <span id="avg-kpi" class="text-lg font-bold text-blue-600">0</span>
                                </td>
                                <td></td>
                            </tr>
                            <tr class="bg-blue-50">
                                <td colspan="4" class="px-4 py-2 text-right font-medium">Nilai KPI × Bobot:</td>
                                <td class="px-4 py-2">
                                    <span id="weighted-kpi" class="text-lg font-bold text-blue-700">0</span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-yellow-800">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Belum ada indikator KPI untuk jabatan ini. Silakan hubungi admin.
                    </p>
                </div>
            @endif
        </div>

        <!-- ==================== KOMPETENSI ==================== -->
        <div class="bg-white rounded-lg shadow p-6 mt-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">
                    <i class="fas fa-brain text-green-600 mr-2"></i>
                    Kompetensi (Bobot: {{ $bobot->bobot_kompetensi ?? 15 }}%)
                </h3>
                <span class="text-xs text-gray-500">Skala 1-5</span>
            </div>
            @if($indikator['kompetensi'] && $indikator['kompetensi']->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">No</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Indikator</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Skala</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Bobot</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Nilai (1-5)</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Komentar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($indikator['kompetensi'] as $index => $ind)
                            <tr>
                                <td class="px-4 py-2 text-sm">{{ $index + 1 }}</td>
                                <td class="px-4 py-2 text-sm">{{ $ind->indikator }}</td>
                                <td class="px-4 py-2 text-sm">1-{{ $ind->skala_maksimal }}</td>
                                <td class="px-4 py-2 text-sm">{{ $ind->bobot }}%</td>
                                <td class="px-4 py-2">
                                    <input type="number" name="nilai_kompetensi[]" required
                                           min="1" max="{{ $ind->skala_maksimal }}" step="0.5"
                                           class="w-24 px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 nilai-input"
                                           data-jenis="kompetensi">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="text" name="komentar_kompetensi[]" 
                                           class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           placeholder="Opsional">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50">
                                <td colspan="4" class="px-4 py-2 text-right font-medium">Rata-rata Nilai Kompetensi:</td>
                                <td class="px-4 py-2">
                                    <span id="avg-kompetensi" class="text-lg font-bold text-green-600">0</span>
                                </td>
                                <td></td>
                            </tr>
                            <tr class="bg-green-50">
                                <td colspan="4" class="px-4 py-2 text-right font-medium">Konversi ke 0-100:</td>
                                <td class="px-4 py-2">
                                    <span id="konversi-kompetensi" class="text-lg font-bold text-green-700">0</span>
                                </td>
                                <td></td>
                            </tr>
                            <tr class="bg-green-100">
                                <td colspan="4" class="px-4 py-2 text-right font-medium">Nilai Kompetensi × Bobot:</td>
                                <td class="px-4 py-2">
                                    <span id="weighted-kompetensi" class="text-lg font-bold text-green-800">0</span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-yellow-800">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Belum ada indikator Kompetensi untuk jabatan ini. Silakan hubungi admin.
                    </p>
                </div>
            @endif
        </div>

        <!-- ==================== CORE VALUES ==================== -->
        <div class="bg-white rounded-lg shadow p-6 mt-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">
                    <i class="fas fa-heart text-yellow-600 mr-2"></i>
                    Core Values (Bobot: {{ $bobot->bobot_core_values ?? 15 }}%)
                </h3>
                <span class="text-xs text-gray-500">Skala 1-5</span>
            </div>
            @if($indikator['core_values'] && $indikator['core_values']->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">No</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Indikator</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Skala</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Bobot</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Nilai (1-5)</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Komentar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($indikator['core_values'] as $index => $ind)
                            <tr>
                                <td class="px-4 py-2 text-sm">{{ $index + 1 }}</td>
                                <td class="px-4 py-2 text-sm">{{ $ind->indikator }}</td>
                                <td class="px-4 py-2 text-sm">1-{{ $ind->skala_maksimal }}</td>
                                <td class="px-4 py-2 text-sm">{{ $ind->bobot }}%</td>
                                <td class="px-4 py-2">
                                    <input type="number" name="nilai_core_values[]" required
                                           min="1" max="{{ $ind->skala_maksimal }}" step="0.5"
                                           class="w-24 px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 nilai-input"
                                           data-jenis="core_values">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="text" name="komentar_core_values[]" 
                                           class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                           placeholder="Opsional">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50">
                                <td colspan="4" class="px-4 py-2 text-right font-medium">Rata-rata Nilai Core Values:</td>
                                <td class="px-4 py-2">
                                    <span id="avg-core-values" class="text-lg font-bold text-yellow-600">0</span>
                                </td>
                                <td></td>
                            </tr>
                            <tr class="bg-yellow-50">
                                <td colspan="4" class="px-4 py-2 text-right font-medium">Konversi ke 0-100:</td>
                                <td class="px-4 py-2">
                                    <span id="konversi-core" class="text-lg font-bold text-yellow-700">0</span>
                                </td>
                                <td></td>
                            </tr>
                            <tr class="bg-yellow-100">
                                <td colspan="4" class="px-4 py-2 text-right font-medium">Nilai Core Values × Bobot:</td>
                                <td class="px-4 py-2">
                                    <span id="weighted-core" class="text-lg font-bold text-yellow-800">0</span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-yellow-800">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Belum ada indikator Core Values untuk jabatan ini. Silakan hubungi admin.
                    </p>
                </div>
            @endif
        </div>

        <div class="mt-6 flex justify-end space-x-2">
            <a href="{{ route('pegawai.penilaian-bawahan.index') }}" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-md">
                Batal
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                <i class="fas fa-save mr-2"></i> Simpan Penilaian
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Data predikat dari PHP
    const predikatData = @json(\App\Models\PredikatKinerja::all());

    // Auto-calculate when nilai changes
    document.querySelectorAll('.nilai-input').forEach(input => {
        input.addEventListener('input', calculateAll);
        input.addEventListener('change', calculateAll);
    });

    function calculateAll() {
        // 1. Calculate average KPI (0-100)
        const kpiInputs = document.querySelectorAll('input[name="nilai_kpi[]"]');
        const avgKPI = calculateAverage(kpiInputs, 0, 100);
        document.getElementById('avg-kpi').textContent = avgKPI.toFixed(2);

        // 2. Calculate average Kompetensi (1-5)
        const kompetensiInputs = document.querySelectorAll('input[name="nilai_kompetensi[]"]');
        const avgKompetensiRaw = calculateAverage(kompetensiInputs, 1, 5);
        const avgKompetensi = (avgKompetensiRaw / 5) * 100;
        document.getElementById('avg-kompetensi').textContent = avgKompetensiRaw.toFixed(2);
        document.getElementById('konversi-kompetensi').textContent = avgKompetensi.toFixed(2);

        // 3. Calculate average Core Values (1-5)
        const coreInputs = document.querySelectorAll('input[name="nilai_core_values[]"]');
        const avgCoreRaw = calculateAverage(coreInputs, 1, 5);
        const avgCore = (avgCoreRaw / 5) * 100;
        document.getElementById('avg-core-values').textContent = avgCoreRaw.toFixed(2);
        document.getElementById('konversi-core').textContent = avgCore.toFixed(2);

        // 4. Calculate weighted scores
        const bobotKPI = {{ $bobot->bobot_kpi ?? 70 }};
        const bobotKompetensi = {{ $bobot->bobot_kompetensi ?? 15 }};
        const bobotCore = {{ $bobot->bobot_core_values ?? 15 }};

        const weightedKPI = avgKPI * bobotKPI / 100;
        const weightedKompetensi = avgKompetensi * bobotKompetensi / 100;
        const weightedCore = avgCore * bobotCore / 100;
        const nilaiTotal = weightedKPI + weightedKompetensi + weightedCore;

        // 5. Display weighted results
        document.getElementById('weighted-kpi').textContent = weightedKPI.toFixed(2);
        document.getElementById('weighted-kompetensi').textContent = weightedKompetensi.toFixed(2);
        document.getElementById('weighted-core').textContent = weightedCore.toFixed(2);

        // 6. Display live results
        document.getElementById('live-kpi').textContent = weightedKPI.toFixed(2);
        document.getElementById('live-kompetensi').textContent = weightedKompetensi.toFixed(2);
        document.getElementById('live-core').textContent = weightedCore.toFixed(2);
        document.getElementById('live-total').textContent = nilaiTotal.toFixed(2);

        // 7. Change color based on total score
        const totalEl = document.getElementById('live-total');
        if (nilaiTotal >= 85) {
            totalEl.className = 'text-3xl font-bold text-green-700';
        } else if (nilaiTotal >= 70) {
            totalEl.className = 'text-3xl font-bold text-yellow-700';
        } else if (nilaiTotal >= 60) {
            totalEl.className = 'text-3xl font-bold text-orange-700';
        } else {
            totalEl.className = 'text-3xl font-bold text-red-700';
        }

        // 8. Determine predikat
        let predikat = null;
        for (const p of predikatData) {
            if (nilaiTotal >= p.batas_bawah && nilaiTotal <= p.batas_atas) {
                predikat = p;
                break;
            }
        }

        const predikatEl = document.getElementById('predikat-live');
        if (predikat) {
            predikatEl.textContent = predikat.nama;
            predikatEl.style.color = predikat.warna_text;
            predikatEl.style.backgroundColor = predikat.warna_latar;
            predikatEl.className = 'px-4 py-2 rounded-full text-sm font-semibold inline-block';
        } else {
            predikatEl.textContent = '-';
            predikatEl.className = 'px-4 py-2 rounded-full text-sm font-semibold inline-block bg-gray-200 text-gray-600';
        }
    }

    function calculateAverage(inputs, min, max) {
        let total = 0;
        let count = 0;
        inputs.forEach(input => {
            const val = parseFloat(input.value);
            if (!isNaN(val) && val >= min && val <= max) {
                total += val;
                count++;
            }
        });
        return count > 0 ? total / count : 0;
    }

    // Initial calculation
    calculateAll();
</script>
@endpush
@endsection