@extends('layouts.pegawai')

@section('title', 'Cuti Saya')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Cuti Saya</h1>
            <p class="text-sm text-gray-500">Kelola pengajuan cuti Anda</p>
        </div>
        <button onclick="openModal()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">
            <i class="fas fa-plus mr-2"></i> Ajukan Cuti
        </button>
    </div>

    <!-- Saldo Cuti -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-sm text-gray-500">Total Cuti</p>
            <p class="text-2xl font-bold text-blue-600">{{ $saldoCuti->total_hari ?? 12 }} hari</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-sm text-gray-500">Digunakan</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $saldoCuti->digunakan ?? 0 }} hari</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-sm text-gray-500">Sisa Cuti</p>
            <p class="text-2xl font-bold text-green-600">{{ $saldoCuti->sisa_hari ?? 12 }} hari</p>
        </div>
    </div>

    <!-- Alert -->
    @php
        $perluUpload = $cuti->getCollection()->filter(function($c) {
            return $c->jenis_cuti === 'sakit'
                && $c->status === 'disetujui'
                && (!$c->surat_dokter || $c->surat_dokter_status === 'ditolak');
        });
    @endphp

    @if($perluUpload->count() > 0)
        <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-4 shadow-sm">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl mr-3 mt-0.5"></i>
                <div class="flex-1">
                    <h3 class="font-semibold text-yellow-800">
                        Anda memiliki {{ $perluUpload->count() }} surat dokter yang perlu diupload
                    </h3>
                    <p class="text-sm text-yellow-700 mt-1">
                        Silakan klik tombol <strong>"Upload Surat Dokter"</strong> pada kolom <strong>Surat</strong> di tabel bawah.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Daftar Cuti -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-800">Riwayat Pengajuan Cuti</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Cuti</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Masuk Kerja</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lama</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Surat</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($cuti as $c)
                    @php
                        $butuhSurat = $c->jenis_cuti === 'sakit'
                                    && $c->status === 'disetujui'
                                    && (!$c->surat_dokter || $c->surat_dokter_status === 'ditolak');
                    @endphp
                    <tr class="{{ $butuhSurat ? 'bg-red-50 border-l-4 border-red-500' : '' }}">
                        <td class="px-4 py-3 text-sm">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 rounded-full text-xs
                                @if($c->jenis_cuti === 'sakit') bg-red-100 text-red-800
                                @else bg-blue-100 text-blue-800
                                @endif">
                                {{ ucfirst($c->jenis_cuti) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            {{ $c->tanggal_mulai->format('d/m/Y') }}
                            <span class="text-gray-400">s/d</span>
                            {{ $c->tanggal_selesai->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if($c->tanggal_masuk_kerja)
                                <span class="px-2 py-1 rounded-full text-xs bg-indigo-100 text-indigo-800">
                                    <i class="fas fa-sign-in-alt mr-1"></i>
                                    {{ $c->tanggal_masuk_kerja->format('d/m/Y') }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm">{{ $c->lama_hari }} hari</td>
                        <td class="px-4 py-3 text-sm">
                            @if($c->status == 'proses')
                                <span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800">Proses</span>
                            @elseif($c->status == 'disetujui')
                                <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">Disetujui</span>
                            @elseif($c->status == 'ditolak')
                                <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-800">Ditolak</span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800">{{ ucfirst($c->status) }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if($c->jenis_cuti === 'sakit')
                                @if($c->surat_dokter && $c->surat_dokter_status === 'disetujui')
                                    <a href="{{ asset('storage/' . $c->surat_dokter) }}" target="_blank"
                                       class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800 hover:bg-green-200">
                                        <i class="fas fa-check-circle mr-1"></i> Disetujui
                                    </a>
                                @elseif($c->surat_dokter && $c->surat_dokter_status === 'menunggu')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i> Menunggu
                                    </span>
                                @elseif($c->surat_dokter && $c->surat_dokter_status === 'ditolak')
                                    <div class="space-y-1">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i> Ditolak
                                        </span>
                                        <a href="{{ route('pegawai.cuti.upload-surat.form', $c->id) }}"
                                           class="inline-flex items-center px-2 py-1 rounded-md text-xs font-semibold bg-orange-500 hover:bg-orange-600 text-white">
                                            <i class="fas fa-redo mr-1"></i> Upload Ulang
                                        </a>
                                    </div>
                                @elseif($c->status === 'disetujui')
                                    <a href="{{ route('pegawai.cuti.upload-surat.form', $c->id) }}"
                                       class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-semibold bg-red-500 hover:bg-red-600 text-white shadow-md animate-pulse">
                                        <i class="fas fa-file-medical mr-1"></i> Upload Surat
                                    </a>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-600">
                                        <i class="fas fa-hourglass-half mr-1"></i> Setelah Disetujui
                                    </span>
                                @endif
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <div class="flex flex-wrap gap-1">
                                <a href="{{ route('pegawai.cuti.approval.show', $c->id) }}"
                                   class="text-blue-600 hover:underline text-xs px-2 py-1 bg-blue-50 rounded">
                                    <i class="fas fa-eye"></i> Detail
                                </a>

                                @if(in_array($c->status, ['pending', 'proses']))
                                    <form action="{{ route('pegawai.cuti.cancel', $c->id) }}" method="POST"
                                          class="inline"
                                          onsubmit="return confirm('Yakin ingin membatalkan cuti ini?')">
                                        @csrf
                                        <button type="submit"
                                                class="text-red-600 hover:underline text-xs px-2 py-1 bg-red-50 rounded">
                                            <i class="fas fa-times"></i> Batalkan
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-10 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2 block text-gray-300"></i>
                            <p>Belum ada pengajuan cuti</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3">{{ $cuti->links() }}</div>
    </div>
</div>

<!-- MODAL AJUKAN CUTI -->
<div id="modalCuti" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl flex flex-col"
             style="max-height: calc(100vh - 2rem);">

            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center flex-shrink-0">
                <h3 class="text-lg font-semibold">
                    <i class="fas fa-calendar-plus text-blue-600 mr-2"></i> Ajukan Cuti
                </h3>
                <button type="button" onclick="closeModal()"
                        class="text-gray-400 hover:text-gray-600 text-2xl leading-none">
                    &times;
                </button>
            </div>

            <div class="overflow-y-auto flex-1 px-6 py-4" style="min-height: 0;">
                <form id="formCuti" action="{{ route('pegawai.cuti.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Cuti *</label>
                        <select name="jenis_cuti" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option value="">Pilih Jenis Cuti</option>
                            <option value="tahunan">Cuti Tahunan</option>
                            <option value="sakit">Cuti Sakit</option>
                            <option value="melahirkan">Cuti Melahirkan</option>
                            <option value="khusus">Cuti Khusus</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai *</label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai" required
                                   min="{{ date('Y-m-d') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lama (Hari Kerja) *</label>
                            <input type="number" name="lama_hari" id="lama_hari" required min="1" value="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                    </div>

                    <div id="info-tanggal" class="hidden bg-blue-50 border border-blue-200 rounded-md p-3 text-sm text-blue-800 space-y-2">
                        <p>
                            <i class="fas fa-calendar-check mr-1"></i>
                            <strong>Tanggal Selesai Cuti:</strong>
                            <span id="tanggal_selesai_text">-</span>
                        </p>
                        <p class="pt-2 border-t border-blue-200">
                            <i class="fas fa-sign-in-alt mr-1"></i>
                            <strong>Tanggal Masuk Kerja:</strong>
                            <span id="tanggal_masuk_text">-</span>
                        </p>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3">
                        <p class="text-sm text-yellow-800">
                            <i class="fas fa-route mr-1"></i>
                            <strong>Tentukan Alur Approval:</strong> Pilih siapa saja yang harus menyetujui cuti ini secara berurutan.
                        </p>
                    </div>

                    <div id="approver-container" class="space-y-3"></div>

                    <button type="button" onclick="tambahApprover()"
                            class="w-full py-2 border-2 border-dashed border-blue-300 text-blue-600 rounded-md hover:bg-blue-50 text-sm">
                        <i class="fas fa-plus mr-1"></i> Tambah Approver
                    </button>

                    <input type="hidden" name="final_index" id="final_index" value="0">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                        <textarea name="keterangan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md"
                                  placeholder="Alasan cuti (opsional)"></textarea>
                    </div>

                    <div id="info-surat-dokter" class="hidden bg-red-50 border border-red-200 rounded-md p-3">
                        <p class="text-sm text-red-800">
                            <i class="fas fa-info-circle mr-1"></i>
                            Untuk cuti sakit, <strong>surat dokter diupload setelah cuti disetujui final</strong>.
                            Tombol upload akan muncul di kolom <strong>Surat</strong>.
                        </p>
                    </div>
                </form>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-2 flex-shrink-0 bg-gray-50 rounded-b-lg">
                <button type="button" onclick="closeModal()"
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-md text-sm">
                    Batal
                </button>
                <button type="submit" form="formCuti"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">
                    <i class="fas fa-paper-plane mr-1"></i> Kirim
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let approverCount = 0;

    function openModal() {
        document.getElementById('modalCuti').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        document.getElementById('approver-container').innerHTML = '';
        approverCount = 0;
        tambahApprover();
    }

    function closeModal() {
        document.getElementById('modalCuti').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function tambahApprover() {
        approverCount++;
        const container = document.getElementById('approver-container');
        const currentIndex = approverCount - 1;

        const div = document.createElement('div');
        div.className = 'approver-row bg-gray-50 border border-gray-200 rounded-md p-3 space-y-2';
        div.dataset.index = currentIndex;

        let options = '<option value="">-- Pilih Pegawai --</option>';
        @foreach($pegawaiLain as $p)
            options += '<option value="{{ $p->id }}">{{ $p->nama }} ({{ $p->nip }})</option>';
        @endforeach

        div.innerHTML = `
            <div class="flex justify-between items-center">
                <span class="text-xs font-semibold text-gray-600 label-approver">
                    <i class="fas fa-user-check mr-1"></i> Approver Level ${approverCount}
                </span>
                <button type="button" onclick="hapusApprover(this)"
                        class="text-red-500 hover:text-red-700 text-xs">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>
            <select name="approvers[]" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                ${options}
            </select>
            <label class="flex items-center space-x-2 text-xs text-purple-700 bg-purple-50 px-2 py-1.5 rounded cursor-pointer">
                <input type="radio" name="final_radio" value="${currentIndex}"
                       onchange="setFinal(${currentIndex})"
                       ${approverCount === 1 ? 'checked' : ''}>
                <span><strong>Approver FINAL</strong> (setelah disetujui, cuti langsung disetujui)</span>
            </label>
        `;

        container.appendChild(div);

        if (approverCount === 1) {
            document.getElementById('final_index').value = 0;
        }

        updateApproverLabels();
        setTimeout(() => div.scrollIntoView({ behavior: 'smooth', block: 'nearest' }), 100);
    }

    function hapusApprover(btn) {
        const rows = document.querySelectorAll('.approver-row');
        if (rows.length <= 1) {
            alert('Minimal harus ada 1 approver!');
            return;
        }

        const row = btn.closest('.approver-row');
        const wasFinal = row.querySelector('input[type="radio"]').checked;

        row.remove();
        approverCount = document.querySelectorAll('.approver-row').length;

        if (wasFinal) {
            const lastRadio = document.querySelector('.approver-row:last-child input[type="radio"]');
            if (lastRadio) {
                lastRadio.checked = true;
                setFinal(lastRadio.value);
            }
        }
        updateApproverLabels();
    }

    function updateApproverLabels() {
        const rows = document.querySelectorAll('.approver-row');
        rows.forEach((row, idx) => {
            const label = row.querySelector('.label-approver');
            if (label) label.innerHTML = `<i class="fas fa-user-check mr-1"></i> Approver Level ${idx + 1}`;
            const radio = row.querySelector('input[type="radio"]');
            if (radio) {
                radio.value = idx;
                radio.setAttribute('onchange', `setFinal(${idx})`);
            }
            row.dataset.index = idx;
        });
    }

    function setFinal(index) {
        document.getElementById('final_index').value = index;
    }

    const elMulai = document.getElementById('tanggal_mulai');
    const elLama = document.getElementById('lama_hari');
    const elJenis = document.querySelector('select[name="jenis_cuti"]');

    if (elMulai) elMulai.addEventListener('change', hitungTanggal);
    if (elLama) elLama.addEventListener('input', hitungTanggal);

    if (elJenis) {
        elJenis.addEventListener('change', function() {
            const info = document.getElementById('info-surat-dokter');
            if (this.value === 'sakit') {
                info.classList.remove('hidden');
            } else {
                info.classList.add('hidden');
            }
        });
    }

    function hitungTanggal() {
        const mulai = document.getElementById('tanggal_mulai').value;
        const lama = document.getElementById('lama_hari').value;

        if (mulai && lama && parseInt(lama) > 0) {
            fetch('{{ route("pegawai.cuti.calculate-date") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ tanggal_mulai: mulai, lama_hari: lama })
            })
            .then(r => r.json())
            .then(data => {
                document.getElementById('info-tanggal').classList.remove('hidden');
                document.getElementById('tanggal_selesai_text').textContent = data.tanggal_selesai_format;
                document.getElementById('tanggal_masuk_text').textContent = data.tanggal_masuk_kerja_format;
            })
            .catch(err => console.error('Error hitung tanggal:', err));
        }
    }

    window.addEventListener('click', function(e) {
        const modal = document.getElementById('modalCuti');
        if (e.target === modal) closeModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });
</script>
@endpush
@endsection