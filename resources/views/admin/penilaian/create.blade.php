@extends('layouts.admin')

@section('title', 'Form Penilaian KPI')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Form Penilaian</h1>
            <p class="text-sm text-gray-500">
                Pegawai: {{ $pegawai->nama }} ({{ $pegawai->nip }}) - 
                Jabatan: {{ $pegawai->jabatan->nama }}
            </p>
        </div>
        <a href="{{ route('admin.penilaian.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md text-sm">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <!-- Info Level Penilai -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center">
            <i class="fas fa-user-check text-blue-600 text-xl mr-3"></i>
            <div>
                <p class="text-sm text-gray-600">Anda menilai sebagai:</p>
                <p class="font-semibold text-blue-800">
                    {{ ucfirst(str_replace('_', ' ', $levelPenilai)) }}
                </p>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.penilaian.store') }}" method="POST" id="formPenilaian">
        @csrf
        <input type="hidden" name="pegawai_id" value="{{ $pegawai->id }}">
        <input type="hidden" name="periode_id" value="{{ $periode->id }}">
        <input type="hidden" name="level_penilai" value="{{ $levelPenilai }}">

        <!-- 1. PENILAIAN KPI -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">
                    <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                    Penilaian KPI
                    <span class="ml-2 text-sm font-normal text-gray-500">(Bobot: {{ $bobot->bobot_kpi ?? 70 }}%)</span>
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">No</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Indikator KPI</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Target</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Bobot %</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Nilai (0-100)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($indikator['kpi'] as $index => $indikator)
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ $indikator->indikator }}</td>
                            <td class="px-4 py-2 text-sm text-gray-500">{{ $indikator->target }} {{ $indikator->satuan }}</td>
                            <td class="px-4 py-2 text-sm text-gray-500">{{ $indikator->bobot }}%</td>
                            <td class="px-4 py-2">
                                <input type="number" name="nilai_kpi[]" required
                                       min="0" max="100" step="0.5"
                                       class="w-24 px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 nilai-input"
                                       data-jenis="kpi">
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-2 text-center text-gray-500">
                                Belum ada indikator KPI untuk jabatan ini.
                                <a href="{{ route('admin.indikator.edit', $pegawai->jabatan_id) }}" class="text-blue-600 hover:underline">
                                    Tambah indikator
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50">
                            <td colspan="4" class="px-4 py-2 text-right font-medium">Rata-rata Nilai KPI:</td>
                            <td class="px-4 py-2">
                                <span id="avg-kpi" class="text-lg font-bold text-blue-600">0</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- 2. PENILAIAN KOMPETENSI -->
        <div class="bg-white rounded-lg shadow p-6 mt-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">
                    <i class="fas fa-brain text-green-600 mr-2"></i>
                    Penilaian Kompetensi
                    <span class="ml-2 text-sm font-normal text-gray-500">(Bobot: {{ $bobot->bobot_kompetensi ?? 15 }}%)</span>
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">No</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Indikator Kompetensi</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Skala</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Bobot %</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($indikator['kompetensi'] as $index => $indikator)
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ $indikator->indikator }}</td>
                            <td class="px-4 py-2 text-sm text-gray-500">1 - {{ $indikator->skala_maksimal }}</td>
                            <td class="px-4 py-2 text-sm text-gray-500">{{ $indikator->bobot }}%</td>
                            <td class="px-4 py-2">
                                <input type="number" name="nilai_kompetensi[]" required
                                       min="1" max="{{ $indikator->skala_maksimal }}" step="0.5"
                                       class="w-24 px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 nilai-input"
                                       data-jenis="kompetensi">
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-2 text-center text-gray-500">
                                Belum ada indikator kompetensi untuk jabatan ini.
                                <a href="{{ route('admin.indikator.edit', $pegawai->jabatan_id) }}" class="text-blue-600 hover:underline">
                                    Tambah indikator
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50">
                            <td colspan="4" class="px-4 py-2 text-right font-medium">Rata-rata Nilai Kompetensi:</td>
                            <td class="px-4 py-2">
                                <span id="avg-kompetensi" class="text-lg font-bold text-green-600">0</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- 3. PENILAIAN CORE VALUES -->
        <div class="bg-white rounded-lg shadow p-6 mt-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">
                    <i class="fas fa-heart text-yellow-600 mr-2"></i>
                    Penilaian Core Values
                    <span class="ml-2 text-sm font-normal text-gray-500">(Bobot: {{ $bobot->bobot_core_values ?? 15 }}%)</span>
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">No</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Indikator Core Values</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Skala</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Bobot %</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($indikator['core_values'] as $index => $indikator)
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ $indikator->indikator }}</td>
                            <td class="px-4 py-2 text-sm text-gray-500">1 - {{ $indikator->skala_maksimal }}</td>
                            <td class="px-4 py-2 text-sm text-gray-500">{{ $indikator->bobot }}%</td>
                            <td class="px-4 py-2">
                                <input type="number" name="nilai_core_values[]" required
                                       min="1" max="{{ $indikator->skala_maksimal }}" step="0.5"
                                       class="w-24 px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 nilai-input"
                                       data-jenis="core_values">
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-2 text-center text-gray-500">
                                Belum ada indikator core values untuk jabatan ini.
                                <a href="{{ route('admin.indikator.edit', $pegawai->jabatan_id) }}" class="text-blue-600 hover:underline">
                                    Tambah indikator
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50">
                            <td colspan="4" class="px-4 py-2 text-right font-medium">Rata-rata Nilai Core Values:</td>
                            <td class="px-4 py-2">
                                <span id="avg-core-values" class="text-lg font-bold text-yellow-600">0</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- HASIL PERHITUNGAN -->
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
                    <p class="text-xs text-gray-500" id="predikat-text">-</p>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-2">
            <a href="{{ route('admin.penilaian.index') }}" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-md">
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
    // Auto-calculate when nilai changes
    document.querySelectorAll('.nilai-input').forEach(input => {
        input.addEventListener('input', calculateAll);
        input.addEventListener('change', calculateAll);
    });

    // Predikat data from PHP
    const predikatData = @json(\App\Models\PredikatKinerja::all());

    function calculateAll() {
        // 1. Calculate average KPI
        const kpiInputs = document.querySelectorAll('input[name="nilai_kpi[]"]');
        const avgKPI = calculateAverage(kpiInputs);
        document.getElementById('avg-kpi').textContent = avgKPI.toFixed(2);

        // 2. Calculate average Kompetensi
        const kompetensiInputs = document.querySelectorAll('input[name="nilai_kompetensi[]"]');
        const avgKompetensi = calculateAverage(kompetensiInputs);
        document.getElementById('avg-kompetensi').textContent = avgKompetensi.toFixed(2);

        // 3. Calculate average Core Values
        const coreInputs = document.querySelectorAll('input[name="nilai_core_values[]"]');
        const avgCore = calculateAverage(coreInputs);
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

        // 6. Determine predikat
        let predikat = null;
        for (const p of predikatData) {
            if (nilaiTotal >= p.batas_bawah && nilaiTotal <= p.batas_atas) {
                predikat = p;
                break;
            }
        }

        const predikatText = document.getElementById('predikat-text');
        if (predikat) {
            predikatText.textContent = predikat.nama;
            predikatText.style.color = predikat.warna_text;
            predikatText.style.backgroundColor = predikat.warna_latar;
            predikatText.className = 'text-xs px-2 py-1 rounded-full inline-block';
        } else {
            predikatText.textContent = '-';
            predikatText.className = 'text-xs text-gray-500';
        }
    }

    function calculateAverage(inputs) {
        let total = 0;
        let count = 0;
        inputs.forEach(input => {
            const val = parseFloat(input.value);
            if (!isNaN(val) && val >= 0) {
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