@extends('layouts.admin')

@section('title', 'Detail Cuti')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detail Cuti</h1>
            <p class="text-sm text-gray-500">Informasi lengkap pengajuan cuti</p>
        </div>
        <a href="{{ route('admin.cuti.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="mb-6 p-4 rounded-lg
        @if($cuti->status == 'proses') bg-yellow-50 border border-yellow-200
        @elseif($cuti->status == 'disetujui') bg-green-50 border border-green-200
        @elseif($cuti->status == 'ditolak') bg-red-50 border border-red-200
        @else bg-gray-50 border border-gray-200
        @endif">
        <div class="flex items-center">
            <div class="flex-shrink-0 mr-3">
                @if($cuti->status == 'proses')
                    <i class="fas fa-clock text-yellow-500 text-2xl"></i>
                @elseif($cuti->status == 'disetujui')
                    <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                @elseif($cuti->status == 'ditolak')
                    <i class="fas fa-times-circle text-red-500 text-2xl"></i>
                @else
                    <i class="fas fa-ban text-gray-500 text-2xl"></i>
                @endif
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-800">
                    Status: <strong>{{ ucfirst($cuti->status) }}</strong>
                    @if($cuti->status == 'proses')
                        — Level saat ini: <strong>{{ $cuti->current_approval_level }}</strong>
                    @endif
                </h3>
                @if($cuti->final_approved_at)
                    <p class="text-xs text-gray-600 mt-1">
                        Final approved oleh <strong>{{ $cuti->finalApprovedBy->nama ?? '-' }}</strong>
                        pada {{ $cuti->final_approved_at->format('d/m/Y H:i') }}
                    </p>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="border rounded-lg p-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-user text-blue-600 mr-2"></i>
                Informasi Pegawai
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Nama</span>
                    <span class="font-medium">{{ $cuti->pegawai->nama ?? '-' }}</span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">NIP</span>
                    <span class="font-medium">{{ $cuti->pegawai->nip ?? '-' }}</span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Jabatan</span>
                    <span class="font-medium">{{ $cuti->pegawai->jabatan->nama ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Kantor</span>
                    <span class="font-medium">{{ $cuti->pegawai->kantor->nama ?? '-' }}</span>
                </div>
            </div>
        </div>

        <div class="border rounded-lg p-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-calendar-alt text-blue-600 mr-2"></i>
                Informasi Cuti
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Jenis Cuti</span>
                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                        {{ ucfirst($cuti->jenis_cuti) }}
                    </span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Tanggal Mulai</span>
                    <span class="font-medium">{{ $cuti->tanggal_mulai->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Tanggal Selesai</span>
                    <span class="font-medium">{{ $cuti->tanggal_selesai->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Tanggal Masuk Kerja</span>
                    <span class="font-medium">
                        {{ $cuti->tanggal_masuk_kerja ? $cuti->tanggal_masuk_kerja->format('d/m/Y') : '-' }}
                    </span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Lama Cuti</span>
                    <span class="font-medium">{{ $cuti->lama_hari }} hari kerja</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Keterangan</span>
                    <span class="font-medium">{{ $cuti->keterangan ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== SURAT DOKTER ==================== --}}
    @if($cuti->jenis_cuti === 'sakit')
    <div class="mt-6 border-2 border-red-200 rounded-lg p-4 bg-red-50">
        <h3 class="text-lg font-semibold text-red-800 mb-3 flex items-center">
            <i class="fas fa-file-medical mr-2"></i>
            Surat Dokter

            @if($cuti->surat_dokter_status === 'disetujui')
                <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-800">
                    <i class="fas fa-check-circle mr-1"></i> Disetujui
                </span>
            @elseif($cuti->surat_dokter_status === 'ditolak')
                <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-red-100 text-red-800">
                    <i class="fas fa-times-circle mr-1"></i> Ditolak
                </span>
            @elseif($cuti->surat_dokter)
                <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-800">
                    <i class="fas fa-clock mr-1"></i> Menunggu Verifikasi
                </span>
            @else
                <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-800">
                    Belum Upload
                </span>
            @endif
        </h3>

        @if($cuti->surat_dokter)
            <div class="bg-white rounded-lg p-4">
                @if($cuti->surat_dokter_is_image)
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $cuti->surat_dokter) }}"
                             alt="Surat Dokter"
                             class="max-w-full h-auto max-h-96 rounded border border-gray-300 mx-auto block">
                    </div>
                @elseif($cuti->surat_dokter_is_pdf)
                    <div class="mb-3">
                        <iframe src="{{ asset('storage/' . $cuti->surat_dokter) }}"
                                class="w-full h-96 border border-gray-300 rounded"></iframe>
                    </div>
                @endif

                <div class="flex flex-wrap justify-center gap-2 mt-4 pt-4 border-t border-gray-200">
                    <a href="{{ asset('storage/' . $cuti->surat_dokter) }}"
                       target="_blank"
                       class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">
                        <i class="fas fa-external-link-alt mr-1"></i> Buka
                    </a>
                    <a href="{{ route('admin.cuti.download-surat-dokter', $cuti->id) }}"
                       class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md text-sm">
                        <i class="fas fa-download mr-1"></i> Download
                    </a>

                    @if($cuti->surat_dokter_status !== 'disetujui')
                        <form action="{{ route('admin.cuti.approve-surat-dokter', $cuti->id) }}" method="POST"
                              class="inline"
                              onsubmit="return confirm('Setujui surat dokter ini?')">
                            @csrf
                            <button type="submit"
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm">
                                <i class="fas fa-check mr-1"></i> Setujui Surat
                            </button>
                        </form>
                    @endif

                    @if($cuti->surat_dokter_status !== 'ditolak')
                        <button type="button" onclick="openRejectSuratModal()"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm">
                            <i class="fas fa-times mr-1"></i> Tolak Surat
                        </button>
                    @endif
                </div>
            </div>
        @else
            <div class="bg-white rounded-lg p-4">
                @if($cuti->status === 'disetujui')
                    <p class="text-sm text-yellow-700 text-center">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Pegawai belum mengupload surat dokter. Cuti sudah disetujui, tunggu pegawai upload.
                    </p>
                @else
                    <p class="text-sm text-gray-600 text-center">
                        <i class="fas fa-hourglass-half mr-1"></i>
                        Surat dokter dapat diupload setelah cuti disetujui final.
                    </p>
                @endif
            </div>
        @endif
    </div>

    {{-- Modal Tolak Surat --}}
    <div id="rejectSuratModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                <form action="{{ route('admin.cuti.reject-surat-dokter', $cuti->id) }}" method="POST">
                    @csrf
                    <div class="px-6 py-4 border-b">
                        <h3 class="text-lg font-semibold text-red-700">
                            <i class="fas fa-times-circle mr-2"></i> Tolak Surat Dokter
                        </h3>
                    </div>
                    <div class="px-6 py-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Alasan Penolakan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="catatan_surat" rows="4" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md"
                                  placeholder="Contoh: Surat kurang jelas, tidak ada tanda tangan dokter, dll."></textarea>
                        <p class="text-xs text-gray-500 mt-2">
                            Pegawai akan melihat alasan ini dan dapat mengupload ulang.
                        </p>
                    </div>
                    <div class="px-6 py-4 border-t bg-gray-50 flex justify-end gap-2">
                        <button type="button" onclick="closeRejectSuratModal()"
                                class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-md text-sm">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm">
                            <i class="fas fa-times mr-1"></i> Tolak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openRejectSuratModal() {
            document.getElementById('rejectSuratModal').classList.remove('hidden');
        }
        function closeRejectSuratModal() {
            document.getElementById('rejectSuratModal').classList.add('hidden');
        }
    </script>
    @endif

    <!-- Alur Approval -->
    <div class="mt-6 border rounded-lg p-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-route text-blue-600 mr-2"></i>
            Alur Approval
        </h3>
        <div class="space-y-3">
            @forelse($cuti->approvals as $ap)
            <div class="border-l-4 pl-4 py-2 rounded-r-md
                @if($ap->status == 'disetujui') border-green-500 bg-green-50
                @elseif($ap->status == 'ditolak') border-red-500 bg-red-50
                @elseif($ap->status == 'pending') border-yellow-500 bg-yellow-50
                @else border-gray-300 bg-gray-50
                @endif">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-medium">
                            Level {{ $ap->level }}: {{ $ap->approver->nama ?? '-' }}
                            @if($ap->is_final)
                                <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-purple-100 text-purple-800">
                                    FINAL
                                </span>
                            @endif
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            Diminta oleh: {{ $ap->requestedBy->nama ?? '-' }}
                            @if($ap->approved_at)
                                | {{ $ap->approved_at->format('d/m/Y H:i') }}
                            @endif
                        </p>
                        @if($ap->catatan)
                        <p class="text-sm text-gray-700 mt-1">
                            <i class="fas fa-comment mr-1"></i> {{ $ap->catatan }}
                        </p>
                        @endif
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full
                        @if($ap->status == 'disetujui') bg-green-100 text-green-800
                        @elseif($ap->status == 'ditolak') bg-red-100 text-red-800
                        @elseif($ap->status == 'pending') bg-yellow-100 text-yellow-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        @if($ap->status == 'menunggu') Menunggu @else {{ ucfirst($ap->status) }} @endif
                    </span>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-500">Belum ada alur approval.</p>
            @endforelse
        </div>
    </div>

    <!-- Aksi -->
    <div class="mt-6 flex flex-wrap gap-2">
        @if($cuti->status == 'disetujui')
            <a href="{{ route('admin.cuti.cetak-surat', $cuti->id) }}" target="_blank"
               class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-md">
                <i class="fas fa-file-pdf mr-2"></i> Preview Surat Cuti
            </a>
            <a href="{{ route('admin.cuti.download-surat', $cuti->id) }}"
               class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md">
                <i class="fas fa-download mr-2"></i> Download Surat Cuti
            </a>
        @endif

        @if($cuti->status == 'proses')
            <form action="{{ route('admin.cuti.approve', $cuti->id) }}" method="POST" class="inline"
                  onsubmit="return confirm('Setujui cuti ini sebagai admin?')">
                @csrf
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md">
                    <i class="fas fa-check mr-2"></i> Setujui (Admin)
                </button>
            </form>
            <form action="{{ route('admin.cuti.reject', $cuti->id) }}" method="POST" class="inline"
                  onsubmit="return confirm('Tolak cuti ini sebagai admin?')">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md">
                    <i class="fas fa-times mr-2"></i> Tolak (Admin)
                </button>
            </form>
            <a href="{{ route('admin.cuti.edit', $cuti->id) }}"
               class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
        @endif

        @if(in_array($cuti->status, ['proses', 'disetujui']))
            <form action="{{ route('admin.cuti.cancel', $cuti->id) }}" method="POST" class="inline"
                  onsubmit="return confirm('Yakin batalkan cuti ini?')">
                @csrf
                <button type="submit" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-md">
                    <i class="fas fa-ban mr-2"></i> Batalkan
                </button>
            </form>
        @endif
    </div>
</div>
@endsection