@extends('layouts.pegawai')

@section('title', 'Review Cuti')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Review Pengajuan Cuti</h1>
            <p class="text-sm text-gray-500">Review dan setujui/tolak pengajuan cuti</p>
        </div>
        <a href="{{ route('pegawai.cuti.approval.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <!-- Status Banner -->
    <div class="p-4 rounded-lg
        @if($cuti->status == 'proses') bg-yellow-50 border border-yellow-200
        @elseif($cuti->status == 'disetujui') bg-green-50 border border-green-200
        @else bg-red-50 border border-red-200
        @endif">
        <p class="text-sm font-medium">
            Status: <strong>{{ ucfirst($cuti->status) }}</strong>
            @if($cuti->status == 'proses')
                — Level saat ini: <strong>{{ $cuti->current_approval_level }}</strong>
            @endif
        </p>
    </div>

    <!-- Info Pegawai -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-user text-blue-600 mr-2"></i> Informasi Pemohon
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Nama</p>
                <p class="font-medium">{{ $cuti->pegawai->nama }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">NIP</p>
                <p class="font-medium">{{ $cuti->pegawai->nip }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Jabatan</p>
                <p class="font-medium">{{ $cuti->pegawai->jabatan->nama ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Jenis Cuti</p>
                <p class="font-medium">{{ ucfirst($cuti->jenis_cuti) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Tanggal Cuti</p>
                <p class="font-medium">
                    {{ $cuti->tanggal_mulai->format('d/m/Y') }} - {{ $cuti->tanggal_selesai->format('d/m/Y') }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Lama</p>
                <p class="font-medium">{{ $cuti->lama_hari }} hari kerja</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Tanggal Masuk Kerja</p>
                <p class="font-medium">
                    {{ $cuti->tanggal_masuk_kerja ? $cuti->tanggal_masuk_kerja->format('d/m/Y') : '-' }}
                </p>
            </div>
            @if($cuti->keterangan)
            <div class="md:col-span-2">
                <p class="text-sm text-gray-500">Keterangan</p>
                <p class="font-medium">{{ $cuti->keterangan }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- ==================== SURAT DOKTER (INFO SAJA) ==================== --}}
    @if($cuti->jenis_cuti === 'sakit')
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-file-medical text-red-600 mr-2"></i>
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
                <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-800">
                    <i class="fas fa-info-circle mr-1"></i> Akan diupload setelah cuti disetujui
                </span>
            @endif
        </h3>

        @if($cuti->surat_dokter)
            <div class="bg-gray-50 rounded-lg p-4">
                @if($cuti->surat_dokter_is_image)
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $cuti->surat_dokter) }}"
                             alt="Surat Dokter"
                             class="max-w-full h-auto max-h-96 rounded border border-gray-300 mx-auto block">
                    </div>
                @elseif($cuti->surat_dokter_is_pdf)
                    <div class="mb-3">
                        <iframe src="{{ asset('storage/' . $cuti->surat_dokter) }}"
                                class="w-full h-96 border border-gray-300 rounded"
                                title="Surat Dokter PDF"></iframe>
                    </div>
                @endif

                <div class="text-center mt-3">
                    <a href="{{ asset('storage/' . $cuti->surat_dokter) }}"
                       target="_blank"
                       class="inline-block px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">
                        <i class="fas fa-external-link-alt mr-1"></i> Buka di Tab Baru
                    </a>
                </div>
            </div>
        @else
            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-1"></i>
                    <strong>Info:</strong> Surat dokter akan diupload oleh pegawai 
                    <strong>setelah cuti ini disetujui final</strong>. 
                    Pengajuan cuti <strong>tidak terhalang</strong> oleh surat dokter.
                </p>
            </div>
        @endif
    </div>
    @endif

    <!-- Alur Approval -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-route text-blue-600 mr-2"></i> Alur Approval
        </h3>
        <div class="space-y-3">
            @foreach($cuti->approvals as $ap)
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
                                | Diproses: {{ $ap->approved_at->format('d/m/Y H:i') }}
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
                        @if($ap->status == 'menunggu') Menunggu
                        @else {{ ucfirst($ap->status) }}
                        @endif
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Form Aksi -->
    @if($myApproval && $cuti->status == 'proses')
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-gavel text-blue-600 mr-2"></i> Aksi Anda
        </h3>

        <div class="mb-4 p-3 rounded-lg
            @if($myApproval->is_final) bg-purple-50 border border-purple-200
            @else bg-blue-50 border border-blue-200
            @endif">
            <p class="text-sm @if($myApproval->is_final) text-purple-800 @else text-blue-800 @endif">
                <i class="fas fa-info-circle mr-1"></i>
                Anda adalah <strong>Approver Level {{ $myApproval->level }}</strong>
                @if($myApproval->is_final)
                    — <strong>FINAL APPROVER</strong>.
                @else
                    — Setelah Anda setujui, cuti diteruskan ke approver berikutnya.
                @endif
            </p>
        </div>

        {{-- Form Approve — TOMBOL SELALU AKTIF --}}
        <form action="{{ route('pegawai.cuti.approval.approve', $cuti->id) }}" method="POST"
              class="mb-6 pb-6 border-b border-gray-200">
            @csrf
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (opsional)</label>
                    <textarea name="catatan" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md"
                              placeholder="Tambahkan catatan..."></textarea>
                </div>
                <div>
                    <button type="submit"
                            class="px-4 py-2 text-white rounded-md
                            @if($myApproval->is_final) bg-purple-600 hover:bg-purple-700
                            @else bg-green-600 hover:bg-green-700
                            @endif">
                        <i class="fas fa-check mr-1"></i>
                        @if($myApproval->is_final) Setujui (FINAL) @else Setujui & Teruskan @endif
                    </button>
                </div>
            </div>
        </form>

        {{-- Form Reject --}}
        <form action="{{ route('pegawai.cuti.approval.reject', $cuti->id) }}" method="POST">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Penolakan *</label>
                <textarea name="catatan" rows="2" required class="w-full px-3 py-2 border border-gray-300 rounded-md"
                          placeholder="Alasan menolak cuti..."></textarea>
            </div>
            <button type="submit" class="mt-2 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                    onclick="return confirm('Yakin ingin menolak cuti ini?')">
                <i class="fas fa-times mr-1"></i> Tolak
            </button>
        </form>
    </div>
    @endif

    @if(!$myApproval && $cuti->status != 'proses')
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
        <p class="text-sm text-gray-700">
            <i class="fas fa-info-circle mr-1"></i>
            Cuti ini sudah selesai diproses dengan status:
            <strong>{{ ucfirst($cuti->status) }}</strong>.
        </p>
    </div>
    @endif
</div>
@endsection