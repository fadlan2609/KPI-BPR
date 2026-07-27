@extends('layouts.admin')

@section('title', 'Edit Indikator Penilaian')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Indikator Penilaian Jabatan</h1>
            <p class="text-sm text-gray-500">
                Jabatan: <span class="font-semibold">{{ $jabatan->nama }}</span>
            </p>
        </div>
        <div class="flex space-x-2">
            <button onclick="openModal('importMultiModal')" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm">
                <i class="fas fa-file-import mr-2"></i> Import All
            </button>
            <button onclick="openModal('importModal')" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">
                <i class="fas fa-file-import mr-2"></i> Import Single
            </button>
            <a href="{{ route('admin.indikator.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md text-sm">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Info Atasan -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600">Atasan Langsung / Penilai:</p>
                <p class="font-medium text-blue-800">
                    {{ $jabatan->atasan->nama ?? 'Tidak ada atasan' }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Atasan Penilai:</p>
                <p class="font-medium text-blue-800">
                    {{ $jabatan->atasan->atasan->nama ?? 'Tidak ada atasan penilai' }}
                </p>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.indikator.update', $jabatan->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <!-- Bobot Penilaian -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Pengaturan Bobot Penilaian</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Bobot KPI (%)</label>
                    <input type="number" name="bobot_kpi" required min="0" max="100" step="0.01"
                           value="{{ old('bobot_kpi', $bobot->bobot_kpi ?? 70) }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Bobot Kompetensi (%)</label>
                    <input type="number" name="bobot_kompetensi" required min="0" max="100" step="0.01"
                           value="{{ old('bobot_kompetensi', $bobot->bobot_kompetensi ?? 15) }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Bobot Core Values (%)</label>
                    <input type="number" name="bobot_core_values" required min="0" max="100" step="0.01"
                           value="{{ old('bobot_core_values', $bobot->bobot_core_values ?? 15) }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <div class="mt-2 text-sm text-gray-500">
                Total harus 100% (Saat ini: <span id="totalBobot">0</span>%)
            </div>
        </div>

        <!-- ==================== INDIKATOR KPI ==================== -->
        <div class="bg-white rounded-lg shadow p-6 mt-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">
                    <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                    Indikator KPI
                </h3>
                <button type="button" onclick="addIndikator('kpi')" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">
                    <i class="fas fa-plus mr-1"></i> Tambah
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Perspektif</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Nama KPI</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Indikator</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Bobot %</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="indikator-kpi-body">
                        @foreach($indikatorKPI as $indikator)
                        <tr>
                            <td>
                                <input type="text" name="indikator_kpi[{{ $indikator->id }}][perspektif]" 
                                       value="{{ $indikator->perspektif ?? 'Keuangan' }}"
                                       class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Perspektif">
                            </td>
                            <td>
                                <input type="text" name="indikator_kpi[{{ $indikator->id }}][nama]" 
                                       value="{{ $indikator->nama ?? $indikator->indikator }}"
                                       class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Nama KPI">
                            </td>
                            <td>
                                <textarea name="indikator_kpi[{{ $indikator->id }}][indikator]" 
                                          class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                          rows="2" placeholder="Deskripsi indikator">{{ $indikator->indikator }}</textarea>
                            </td>
                            <td>
                                <input type="number" name="indikator_kpi[{{ $indikator->id }}][bobot]" 
                                       value="{{ $indikator->bobot }}" min="0" max="100" step="0.01"
                                       class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </td>
                            <td>
                                <button type="button" onclick="removeIndikator(this, 'kpi', {{ $indikator->id }})" 
                                        class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ==================== INDIKATOR KOMPETENSI ==================== -->
        <div class="bg-white rounded-lg shadow p-6 mt-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">
                    <i class="fas fa-brain text-green-600 mr-2"></i>
                    Indikator Kompetensi
                </h3>
                <button type="button" onclick="addIndikator('kompetensi')" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm">
                    <i class="fas fa-plus mr-1"></i> Tambah
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Perspektif</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Nama Kompetensi</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Indikator</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Bobot %</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="indikator-kompetensi-body">
                        @foreach($indikatorKompetensi as $indikator)
                        <tr>
                            <td>
                                <input type="text" name="indikator_kompetensi[{{ $indikator->id }}][perspektif]" 
                                       value="{{ $indikator->perspektif ?? 'Komunikasi' }}"
                                       class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Perspektif">
                            </td>
                            <td>
                                <input type="text" name="indikator_kompetensi[{{ $indikator->id }}][nama]" 
                                       value="{{ $indikator->nama ?? $indikator->indikator }}"
                                       class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Nama Kompetensi">
                            </td>
                            <td>
                                <textarea name="indikator_kompetensi[{{ $indikator->id }}][indikator]" 
                                          class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                          rows="2" placeholder="Deskripsi indikator">{{ $indikator->indikator }}</textarea>
                            </td>
                            <td>
                                <input type="number" name="indikator_kompetensi[{{ $indikator->id }}][bobot]" 
                                       value="{{ $indikator->bobot }}" min="0" max="100" step="0.01"
                                       class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </td>
                            <td>
                                <button type="button" onclick="removeIndikator(this, 'kompetensi', {{ $indikator->id }})" 
                                        class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ==================== INDIKATOR CORE VALUES ==================== -->
        <div class="bg-white rounded-lg shadow p-6 mt-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">
                    <i class="fas fa-heart text-yellow-600 mr-2"></i>
                    Indikator Core Values
                </h3>
                <button type="button" onclick="addIndikator('core_values')" class="px-3 py-1 bg-yellow-600 hover:bg-yellow-700 text-white rounded-md text-sm">
                    <i class="fas fa-plus mr-1"></i> Tambah
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Perspektif</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Nama Core Value</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Indikator</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Bobot %</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="indikator-core-values-body">
                        @foreach($indikatorCoreValues as $indikator)
                        <tr>
                            <td>
                                <input type="text" name="indikator_core_values[{{ $indikator->id }}][perspektif]" 
                                       value="{{ $indikator->perspektif ?? 'Service Excellence' }}"
                                       class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Perspektif">
                            </td>
                            <td>
                                <input type="text" name="indikator_core_values[{{ $indikator->id }}][nama]" 
                                       value="{{ $indikator->nama ?? $indikator->indikator }}"
                                       class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Nama Core Value">
                            </td>
                            <td>
                                <textarea name="indikator_core_values[{{ $indikator->id }}][indikator]" 
                                          class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                          rows="2" placeholder="Deskripsi indikator">{{ $indikator->indikator }}</textarea>
                            </td>
                            <td>
                                <input type="number" name="indikator_core_values[{{ $indikator->id }}][bobot]" 
                                       value="{{ $indikator->bobot }}" min="0" max="100" step="0.01"
                                       class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </td>
                            <td>
                                <button type="button" onclick="removeIndikator(this, 'core_values', {{ $indikator->id }})" 
                                        class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-2">
            <a href="{{ route('admin.indikator.index') }}" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-md">
                Batal
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                <i class="fas fa-save mr-2"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<!-- ==================== MODAL IMPORT SINGLE SHEET ==================== -->
<div id="importModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-white rounded-t-lg">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-file-import text-blue-600 mr-2"></i>
                    Import Indikator (Single Sheet)
                </h3>
                <button onclick="closeModal('importModal')" class="text-gray-400 hover:text-gray-600 text-2xl transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-6">
                <form action="{{ route('admin.indikator.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="jabatan_id" value="{{ $jabatan->id }}">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Indikator <span class="text-red-500">*</span></label>
                            <select name="jenis" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                <option value="">Pilih Jenis</option>
                                <option value="kpi">KPI</option>
                                <option value="kompetensi">Kompetensi</option>
                                <option value="core_values">Core Values</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">File Excel <span class="text-red-500">*</span></label>
                            <input type="file" name="file" accept=".xlsx,.xls" required
                                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="mt-1 text-xs text-gray-500">Format: .xlsx, .xls | Max: 5MB</p>
                        </div>
                        
                        <div class="bg-blue-50 rounded-lg p-3">
                            <p class="text-sm text-blue-800">
                                <i class="fas fa-info-circle mr-2"></i>
                                Format: <strong>Perspektif | Nama | Indikator | Bobot</strong>
                            </p>
                        </div>
                        
                        <div>
                            <a href="{{ route('admin.indikator.template', 'kpi') }}" class="text-blue-600 hover:underline text-sm">
                                <i class="fas fa-download mr-1"></i> Template KPI
                            </a>
                            <span class="text-gray-300 mx-1">|</span>
                            <a href="{{ route('admin.indikator.template', 'kompetensi') }}" class="text-blue-600 hover:underline text-sm">
                                <i class="fas fa-download mr-1"></i> Template Kompetensi
                            </a>
                            <span class="text-gray-300 mx-1">|</span>
                            <a href="{{ route('admin.indikator.template', 'core_values') }}" class="text-blue-600 hover:underline text-sm">
                                <i class="fas fa-download mr-1"></i> Template Core Values
                            </a>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end space-x-2">
                        <button type="button" onclick="closeModal('importModal')" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-md transition">
                            <i class="fas fa-times mr-2"></i> Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">
                            <i class="fas fa-upload mr-2"></i> Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ==================== MODAL IMPORT MULTI SHEET ==================== -->
<div id="importMultiModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-white rounded-t-lg">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-file-import text-green-600 mr-2"></i>
                    Import All Indikator (Multi Sheet)
                </h3>
                <button onclick="closeModal('importMultiModal')" class="text-gray-400 hover:text-gray-600 text-2xl transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-6">
                <form action="{{ route('admin.indikator.import-multi') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="jabatan_id" value="{{ $jabatan->id }}">
                    
                    <div class="space-y-4">
                        <div class="bg-blue-50 rounded-lg p-3">
                            <p class="text-sm text-blue-800">
                                <i class="fas fa-info-circle mr-2"></i>
                                File Excel harus memiliki 3 sheet:
                            </p>
                            <ul class="text-sm text-blue-700 ml-6 mt-1 list-disc">
                                <li>Sheet 1: <strong>KPI</strong></li>
                                <li>Sheet 2: <strong>Kompetensi</strong></li>
                                <li>Sheet 3: <strong>Core Values</strong></li>
                            </ul>
                            <p class="text-sm text-blue-700 mt-1">
                                Format setiap sheet: <strong>Perspektif | Nama | Indikator | Bobot</strong>
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">File Excel <span class="text-red-500">*</span></label>
                            <input type="file" name="file" accept=".xlsx,.xls" required
                                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="mt-1 text-xs text-gray-500">Format: .xlsx, .xls | Max: 5MB</p>
                        </div>
                        
                        <div>
                            <a href="{{ route('admin.indikator.template-multi') }}" class="text-blue-600 hover:underline text-sm">
                                <i class="fas fa-download mr-1"></i> Download Template Multi Sheet
                            </a>
                        </div>
                        
                        <div class="bg-yellow-50 rounded-lg p-3 border border-yellow-200">
                            <p class="text-sm text-yellow-800">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <strong>Perhatian:</strong> Import akan menghapus semua indikator lama untuk jabatan ini dan menggantinya dengan data baru.
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end space-x-2">
                        <button type="button" onclick="closeModal('importMultiModal')" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-md transition">
                            <i class="fas fa-times mr-2"></i> Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition">
                            <i class="fas fa-upload mr-2"></i> Import All
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // ==================== AUTO-CALCULATE BOBOT ====================
    document.querySelectorAll('input[name^="bobot"]').forEach(input => {
        input.addEventListener('input', calculateTotalBobot);
    });
    
    function calculateTotalBobot() {
        const kpi = parseFloat(document.querySelector('input[name="bobot_kpi"]').value) || 0;
        const kompetensi = parseFloat(document.querySelector('input[name="bobot_kompetensi"]').value) || 0;
        const core = parseFloat(document.querySelector('input[name="bobot_core_values"]').value) || 0;
        const total = kpi + kompetensi + core;
        const el = document.getElementById('totalBobot');
        el.textContent = total.toFixed(2);
        el.className = total === 100 ? 'text-green-600 font-bold' : 'text-red-600 font-bold';
    }
    calculateTotalBobot();
    
    // ==================== ADD INDIKATOR ====================
    function addIndikator(jenis) {
        const tbody = document.getElementById(`indikator-${jenis}-body`);
        const newId = 'new_' + Date.now();
        let html = '';
        
        if (jenis === 'kpi') {
            html = `
                <tr>
                    <td><input type="text" name="indikator_kpi[${newId}][perspektif]" placeholder="Perspektif" class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"></td>
                    <td><input type="text" name="indikator_kpi[${newId}][nama]" placeholder="Nama KPI" class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"></td>
                    <td><textarea name="indikator_kpi[${newId}][indikator]" rows="2" placeholder="Deskripsi indikator" class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea></td>
                    <td><input type="number" name="indikator_kpi[${newId}][bobot]" placeholder="Bobot %" min="0" max="100" step="0.01" class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"></td>
                    <td><button type="button" onclick="this.closest('tr').remove()" class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></button></td>
                </tr>
            `;
        } else if (jenis === 'kompetensi') {
            html = `
                <tr>
                    <td><input type="text" name="indikator_kompetensi[${newId}][perspektif]" placeholder="Perspektif" class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"></td>
                    <td><input type="text" name="indikator_kompetensi[${newId}][nama]" placeholder="Nama Kompetensi" class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"></td>
                    <td><textarea name="indikator_kompetensi[${newId}][indikator]" rows="2" placeholder="Deskripsi indikator" class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea></td>
                    <td><input type="number" name="indikator_kompetensi[${newId}][bobot]" placeholder="Bobot %" min="0" max="100" step="0.01" class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"></td>
                    <td><button type="button" onclick="this.closest('tr').remove()" class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></button></td>
                </tr>
            `;
        } else if (jenis === 'core_values') {
            html = `
                <tr>
                    <td><input type="text" name="indikator_core_values[${newId}][perspektif]" placeholder="Perspektif" class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"></td>
                    <td><input type="text" name="indikator_core_values[${newId}][nama]" placeholder="Nama Core Value" class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"></td>
                    <td><textarea name="indikator_core_values[${newId}][indikator]" rows="2" placeholder="Deskripsi indikator" class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea></td>
                    <td><input type="number" name="indikator_core_values[${newId}][bobot]" placeholder="Bobot %" min="0" max="100" step="0.01" class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"></td>
                    <td><button type="button" onclick="this.closest('tr').remove()" class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></button></td>
                </tr>
            `;
        }
        tbody.insertAdjacentHTML('beforeend', html);
    }
    
    // ==================== REMOVE INDIKATOR ====================
    function removeIndikator(button, jenis, id) {
        if (!confirm('Yakin ingin menghapus indikator ini?')) return;
        const row = button.closest('tr');
        if (id && id.toString().startsWith('new_')) { row.remove(); return; }
        if (id) {
            fetch('/admin/indikator/' + id + '?jenis=' + jenis, {
                method: 'DELETE',
                headers: { 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                    'Content-Type': 'application/json' 
                }
            }).then(r => r.json()).then(d => { 
                if (d.success) { 
                    row.remove(); 
                    showNotification('Indikator berhasil dihapus!', 'success');
                } else { 
                    alert('Gagal: ' + d.message); 
                } 
            }).catch(() => alert('Terjadi kesalahan'));
        }
    }
    
    // ==================== NOTIFICATION ====================
    function showNotification(message, type) {
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500'
        };
        const div = document.createElement('div');
        div.className = `fixed top-20 right-4 px-6 py-3 rounded-lg text-white ${colors[type] || 'bg-gray-500'} z-50 transition-opacity duration-500 shadow-lg`;
        div.textContent = message;
        document.body.appendChild(div);
        setTimeout(() => { div.style.opacity = '0'; setTimeout(() => div.remove(), 500); }, 3000);
    }
    
    // ==================== MODAL ====================
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.body.style.overflow = '';
    }

    window.onclick = function(event) {
        if (event.target.classList.contains('fixed')) {
            document.querySelectorAll('.fixed').forEach(function(modal) {
                modal.classList.add('hidden');
            });
            document.body.style.overflow = '';
        }
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            document.querySelectorAll('.fixed').forEach(function(modal) {
                modal.classList.add('hidden');
            });
            document.body.style.overflow = '';
        }
    });
</script>
@endpush
@endsection