@extends('layouts.pegawai')

@section('title', 'Self Assessment')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Self Assessment</h1>
            <p class="text-sm text-gray-500">
                Periode: {{ $periodeAktif->nama ?? '-' }} | Pegawai: {{ $pegawai->nama ?? '-' }}
            </p>
        </div>
        <a href="{{ route('pegawai.dashboard') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md text-sm">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <!-- Info -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <p class="text-sm text-blue-800">
            <i class="fas fa-info-circle mr-2"></i>
            Silakan isi penilaian diri Anda secara jujur dan objektif.
        </p>
    </div>

    <form action="{{ route('pegawai.self-assessment.store') }}" method="POST" id="formSelfAssessment">
        @csrf

        <!-- ==================== 1. PENILAIAN KPI ==================== -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">
                    <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                    Penilaian KPI
                    <span class="ml-2 text-sm font-normal text-gray-500">(Bobot: {{ $bobot->bobot_kpi ?? 70 }}%)</span>
                </h3>
            </div>
            @if($indikator['kpi'] && $indikator['kpi']->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">No</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Indikator KPI</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Target</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Bobot %</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Nilai (0-100)</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Komentar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($indikator['kpi'] as $index => $indikatorItem)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $indikatorItem->indikator }}</td>
                                <td class="px-4 py-2 text-sm text-gray-500">{{ $indikatorItem->target }} {{ $indikatorItem->satuan }}</td>
                                <td class="px-4 py-2 text-sm text-gray-500">{{ $indikatorItem->bobot }}%</td>
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
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-yellow-800">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Belum ada indikator KPI untuk jabatan Anda. Silakan hubungi admin.
                    </p>
                </div>
            @endif
        </div>

        <!-- ==================== 2. PENILAIAN KOMPETENSI ==================== -->
        <div class="bg-white rounded-lg shadow p-6 mt-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">
                    <i class="fas fa-brain text-green-600 mr-2"></i>
                    Penilaian Kompetensi
                    <span class="ml-2 text-sm font-normal text-gray-500">(Bobot: {{ $bobot->bobot_kompetensi ?? 15 }}%)</span>
                </h3>
            </div>
            @if($indikator['kompetensi'] && $indikator['kompetensi']->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">No</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Indikator Kompetensi</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Skala</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Bobot %</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Nilai (1-5)</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Komentar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($indikator['kompetensi'] as $index => $indikatorItem)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $indikatorItem->indikator }}</td>
                                <td class="px-4 py-2 text-sm text-gray-500">1 - {{ $indikatorItem->skala_maksimal }}</td>
                                <td class="px-4 py-2 text-sm text-gray-500">{{ $indikatorItem->bobot }}%</td>
                                <td class="px-4 py-2">
                                    <input type="number" name="nilai_kompetensi[]" required
                                           min="1" max="{{ $indikatorItem->skala_maksimal }}" step="0.5"
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
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-yellow-800">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Belum ada indikator kompetensi untuk jabatan Anda. Silakan hubungi admin.
                    </p>
                </div>
            @endif
        </div>

        <!-- ==================== 3. PENILAIAN CORE VALUES ==================== -->
        <div class="bg-white rounded-lg shadow p-6 mt-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">
                    <i class="fas fa-heart text-yellow-600 mr-2"></i>
                    Penilaian Core Values
                    <span class="ml-2 text-sm font-normal text-gray-500">(Bobot: {{ $bobot->bobot_core_values ?? 15 }}%)</span>
                </h3>
            </div>
            @if($indikator['core_values'] && $indikator['core_values']->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">No</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Indikator Core Values</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Skala</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Bobot %</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Nilai (1-5)</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Komentar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($indikator['core_values'] as $index => $indikatorItem)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $indikatorItem->indikator }}</td>
                                <td class="px-4 py-2 text-sm text-gray-500">1 - {{ $indikatorItem->skala_maksimal }}</td>
                                <td class="px-4 py-2 text-sm text-gray-500">{{ $indikatorItem->bobot }}%</td>
                                <td class="px-4 py-2">
                                    <input type="number" name="nilai_core_values[]" required
                                           min="1" max="{{ $indikatorItem->skala_maksimal }}" step="0.5"
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
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-yellow-800">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Belum ada indikator core values untuk jabatan Anda. Silakan hubungi admin.
                    </p>
                </div>
            @endif
        </div>

        <!-- ==================== HASIL PERHITUNGAN ==================== -->
        <div class="bg-white rounded-lg shadow p-6 mt-4 border-2 border-blue-200">
            <h3 class="text-lg font-semibold mb-4">Hasil Perhitungan</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="p-3 bg-blue-50 rounded-lg">
                    <p class="text-sm text-gray-600">Nilai KPI</p>
                    <p class="text-xl font-bold text-blue-600" id="hasil-kpi">0</p>
                    <p class="text-xs text-gray-500">× {{ $bobot->bobot_kpi ?? 70 }}%</p>
                </div>
                <div class="p-3 bg-green-50 rounded-lg">
                    <p class="text-sm text-gray-600">Nilai Kompetensi</p>
                    <p class="text-xl font-bold text-green-600" id="hasil-kompetensi">0</p>
                    <p class="text-xs text-gray-500">× {{ $bobot->bobot_kompetensi ?? 15 }}%</p>
                </div>
                <div class="p-3 bg-yellow-50 rounded-lg">
                    <p class="text-sm text-gray-600">Nilai Core Values</p>
                    <p class="text-xl font-bold text-yellow-600" id="hasil-core">0</p>
                    <p class="text-xs text-gray-500">× {{ $bobot->bobot_core_values ?? 15 }}%</p>
                </div>
                <div class="p-3 bg-purple-50 rounded-lg border-2 border-purple-300">
                    <p class="text-sm text-gray-600">NILAI TOTAL</p>
                    <p class="text-2xl font-bold text-purple-700" id="hasil-total">0</p>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-2">
            <a href="{{ route('pegawai.dashboard') }}" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-md">
                Batal
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md" id="btnSubmit">
                <i class="fas fa-save mr-2"></i> Simpan Self Assessment
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
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

        // 2. Calculate average Kompetensi (1-5) - konversi ke 0-100
        const kompetensiInputs = document.querySelectorAll('input[name="nilai_kompetensi[]"]');
        const avgKompetensiRaw = calculateAverage(kompetensiInputs, 1, 5);
        const avgKompetensi = (avgKompetensiRaw / 5) * 100;
        document.getElementById('avg-kompetensi').textContent = avgKompetensi.toFixed(2);

        // 3. Calculate average Core Values (1-5) - konversi ke 0-100
        const coreInputs = document.querySelectorAll('input[name="nilai_core_values[]"]');
        const avgCoreRaw = calculateAverage(coreInputs, 1, 5);
        const avgCore = (avgCoreRaw / 5) * 100;
        document.getElementById('avg-core-values').textContent = avgCore.toFixed(2);

        // 4. Calculate weighted scores
        const bobotKPI = {{ $bobot->bobot_kpi ?? 70 }};
        const bobotKompetensi = {{ $bobot->bobot_kompetensi ?? 15 }};
        const bobotCore = {{ $bobot->bobot_core_values ?? 15 }};

        const nilaiKPI = avgKPI * bobotKPI / 100;
        const nilaiKompetensi = avgKompetensi * bobotKompetensi / 100;
        const nilaiCore = avgCore * bobotCore / 100;
        const nilaiTotal = nilaiKPI + nilaiKompetensi + nilaiCore;

        // 5. Display results
        document.getElementById('hasil-kpi').textContent = nilaiKPI.toFixed(2);
        document.getElementById('hasil-kompetensi').textContent = nilaiKompetensi.toFixed(2);
        document.getElementById('hasil-core').textContent = nilaiCore.toFixed(2);
        document.getElementById('hasil-total').textContent = nilaiTotal.toFixed(2);
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