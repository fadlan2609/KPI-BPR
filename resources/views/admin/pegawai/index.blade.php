@extends('layouts.admin')

@section('title', 'Daftar Pegawai')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Daftar Pegawai</h1>
        <div class="flex space-x-2">
            <button onclick="openModal('importModal')" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm">
                <i class="fas fa-file-import mr-2"></i> Import
            </button>
            <button onclick="openModal('tambahModal')" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">
                <i class="fas fa-plus mr-2"></i> Tambah Pegawai
            </button>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-lg shadow p-4">
        <form action="{{ route('admin.pegawai.index') }}" method="GET" class="flex flex-wrap gap-4">
            <input type="text" name="search" placeholder="Cari pegawai..." 
                   value="{{ $search ?? '' }}"
                   class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 flex-1 min-w-[200px]">
            
            <select name="jabatan" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Jabatan</option>
                @foreach($jabatanOptions as $j)
                    <option value="{{ $j->id }}" {{ (isset($jabatan) && $jabatan == $j->id) ? 'selected' : '' }}>
                        {{ $j->nama }}
                    </option>
                @endforeach
            </select>
            
            <select name="status" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Status</option>
                <option value="aktif" {{ (isset($status) && $status == 'aktif') ? 'selected' : '' }}>Aktif</option>
                <option value="keluar" {{ (isset($status) && $status == 'keluar') ? 'selected' : '' }}>Keluar</option>
                <option value="mengundurkan_diri" {{ (isset($status) && $status == 'mengundurkan_diri') ? 'selected' : '' }}>Mengundurkan Diri</option>
            </select>
            
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                <i class="fas fa-search mr-2"></i> Cari
            </button>
            
            <a href="{{ route('admin.pegawai.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md">
                <i class="fas fa-undo mr-2"></i> Reset
            </a>
        </form>
        
        @if($search || $jabatan || $status)
            <div class="mt-2 text-sm text-gray-500">
                <i class="fas fa-filter mr-1"></i>
                Menampilkan hasil filter: 
                @if($search) <span class="font-medium">"{{ $search }}"</span> @endif
                @if($jabatan) <span class="font-medium">| Jabatan: {{ $jabatanOptions->firstWhere('id', $jabatan)->nama ?? '' }}</span> @endif
                @if($status) <span class="font-medium">| Status: {{ ucfirst($status) }}</span> @endif
            </div>
        @endif
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jabatan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kantor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="min-width: 250px;">Atasan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($pegawai as $p)
                @php
                    $isNonAktif = $p->status != 'aktif';
                    $rowClass = $isNonAktif ? 'bg-gray-50 opacity-70' : '';
                @endphp
                <tr class="{{ $rowClass }}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            @if($isNonAktif)
                                <span class="mr-2 text-gray-400">
                                    <i class="fas fa-user-slash"></i>
                                </span>
                            @else
                                <span class="mr-2 text-green-500">
                                    <i class="fas fa-user-check"></i>
                                </span>
                            @endif
                            <div>
                                <div class="text-sm font-medium {{ $isNonAktif ? 'text-gray-500 line-through' : 'text-gray-900' }}">
                                    {{ $p->nama }}
                                </div>
                            </div>
                            @if($isNonAktif)
                                <span class="ml-2 px-2 py-0.5 text-xs bg-red-100 text-red-600 rounded-full">Non Aktif</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm {{ $isNonAktif ? 'text-gray-400' : 'text-gray-500' }}">
                        {{ $p->nip }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm {{ $isNonAktif ? 'text-gray-400' : 'text-gray-500' }}">
                        {{ $p->jabatan->nama ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm {{ $isNonAktif ? 'text-gray-400' : 'text-gray-500' }}">
                        {{ $p->kantor->nama ?? '-' }}
                    </td>
                    
                    <!-- ====== BAGIAN ATASAN YANG DIPERBAIKI ====== -->
                    <td class="px-6 py-4">
                        <div class="flex flex-col space-y-2 min-w-[220px]">
                            <!-- Atasan Langsung -->
                            <div class="flex items-center gap-2">
                                <div class="flex items-center gap-1.5 min-w-[70px]">
                                    <span class="text-xs font-medium text-gray-400">Langsung</span>
                                    <div class="w-2 h-2 rounded-full bg-blue-400"></div>
                                </div>
                                <div class="flex items-center gap-2 flex-1">
                                    @if($p->atasanLangsung)
                                        <span class="text-sm font-semibold text-blue-700 hover:text-blue-900 transition truncate max-w-[150px]" title="{{ $p->atasanLangsung->nama }}">
                                            {{ $p->atasanLangsung->nama }}
                                        </span>
                                        <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full truncate max-w-[100px]" title="{{ $p->atasanLangsung->jabatan->nama ?? '-' }}">
                                            {{ $p->atasanLangsung->jabatan->nama ?? '-' }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-400 italic">- Tidak Ada -</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Garis penghubung -->
                            <div class="flex items-center gap-2 pl-[70px]">
                                <div class="flex-1 h-px bg-gradient-to-r from-blue-200 to-purple-200"></div>
                                <span class="text-[10px] text-gray-400 font-medium px-1">▾</span>
                                <div class="flex-1 h-px bg-gradient-to-r from-purple-200 to-transparent"></div>
                            </div>

                            <!-- Atasan Penilai (Atasan dari Atasan Langsung) -->
                            <div class="flex items-center gap-2">
                                <div class="flex items-center gap-1.5 min-w-[70px]">
                                    <span class="text-xs font-medium text-gray-400">Penilai</span>
                                    <div class="w-2 h-2 rounded-full bg-purple-400"></div>
                                </div>
                                <div class="flex items-center gap-2 flex-1">
                                    @if(isset($p->atasan_penilai) && $p->atasan_penilai)
                                        <span class="text-sm font-semibold text-purple-700 hover:text-purple-900 transition truncate max-w-[150px]" title="{{ $p->atasan_penilai->nama }}">
                                            {{ $p->atasan_penilai->nama }}
                                        </span>
                                        <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full truncate max-w-[100px]" title="{{ $p->atasan_penilai->jabatan->nama ?? '-' }}">
                                            {{ $p->atasan_penilai->jabatan->nama ?? '-' }}
                                        </span>
                                        <span class="ml-1 px-2 py-0.5 text-[10px] bg-purple-100 text-purple-600 rounded-full font-medium whitespace-nowrap">
                                            <i class="fas fa-check-circle mr-0.5"></i> Penilai
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-400 italic">- Tidak Ada -</span>
                                        @if($p->atasanLangsung && !isset($p->atasan_penilai))
                                            <span class="ml-1 px-2 py-0.5 text-[10px] bg-yellow-100 text-yellow-600 rounded-full font-medium whitespace-nowrap">
                                                <i class="fas fa-info-circle mr-0.5"></i> Puncak
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <!-- Info tambahan: jumlah bawahan -->
                            @if($p->bawahanLangsung->count() > 0)
                                <div class="flex items-center gap-2 pl-[70px] mt-1">
                                    <div class="flex items-center gap-1.5">
                                        <i class="fas fa-users text-xs text-gray-400"></i>
                                        <span class="text-xs text-gray-400">
                                            {{ $p->bawahanLangsung->count() }} bawahan langsung
                                            @if(isset($p->total_bawahan_tidak_langsung) && $p->total_bawahan_tidak_langsung > 0)
                                                <span class="mx-1">·</span>
                                                {{ $p->total_bawahan_tidak_langsung }} tidak langsung
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </td>
                    <!-- ====== END BAGIAN ATASAN ====== -->
                    
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($p->status == 'aktif')
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-circle text-green-500 mr-1 text-[6px]"></i>
                                Aktif
                            </span>
                        @elseif($p->status == 'keluar')
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-circle text-red-500 mr-1 text-[6px]"></i>
                                Keluar
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                <i class="fas fa-circle text-yellow-500 mr-1 text-[6px]"></i>
                                Mengundurkan Diri
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($p->user)
                            <span class="text-green-600">{{ $p->user->username }}</span>
                            <form action="{{ route('admin.pegawai.reset-password', $p->id) }}" method="POST" class="inline-block ml-2">
                                @csrf
                                <input type="hidden" name="password" value="password123">
                                <input type="hidden" name="password_confirmation" value="password123">
                                <button type="submit" class="text-yellow-600 hover:text-yellow-900" onclick="return confirm('Reset password untuk {{ $p->nama }}? Password baru: password123')">
                                    <i class="fas fa-key"></i>
                                </button>
                            </form>
                        @else
                            <button onclick="openCreateUserModal({{ $p->id }}, '{{ $p->nama }}', '{{ $p->nip }}')" 
                                    class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-user-plus"></i> Buat Akun
                            </button>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('admin.pegawai.edit', $p->id) }}" class="text-blue-600 hover:text-blue-900 mr-2">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.pegawai.destroy', $p->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus pegawai ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                        @if($search || $jabatan || $status)
                            Tidak ada data pegawai yang sesuai dengan filter
                        @else
                            Belum ada data pegawai
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-3">
            {{ $pegawai->links() }}
        </div>
    </div>

    <!-- Statistik Ringkas -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $pegawai->total() }}</p>
            <p class="text-sm text-gray-500">Total Pegawai</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-green-600">
                {{ $pegawai->where('status', 'aktif')->count() }}
            </p>
            <p class="text-sm text-gray-500">Aktif</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-red-600">
                {{ $pegawai->where('status', 'keluar')->count() }}
            </p>
            <p class="text-sm text-gray-500">Keluar</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-yellow-600">
                {{ $pegawai->where('status', 'mengundurkan_diri')->count() }}
            </p>
            <p class="text-sm text-gray-500">Mengundurkan Diri</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-purple-600">
                {{ $pegawai->filter(function($p) { return $p->bawahanLangsung->count() > 0; })->count() }}
            </p>
            <p class="text-sm text-gray-500">Memiliki Bawahan</p>
        </div>
    </div>
</div>

<!-- ====== MODAL TAMBAH PEGAWAI ====== -->
<div id="tambahModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-5xl" style="max-height: 95vh; display: flex; flex-direction: column;">
            
            <div class="flex-shrink-0 px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-white rounded-t-lg">
                <h3 class="text-xl font-semibold text-gray-900">
                    <i class="fas fa-user-plus text-blue-600 mr-2"></i>
                    Tambah Pegawai Baru
                </h3>
                <button onclick="closeModal('tambahModal')" class="text-gray-400 hover:text-gray-600 text-2xl transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-6 py-4" style="max-height: calc(95vh - 130px);">
                <form action="{{ route('admin.pegawai.store') }}" method="POST" id="formTambahPegawai">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Data Wajib -->
                        <div class="col-span-2">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="h-6 w-1 bg-blue-600 rounded"></div>
                                <h4 class="font-semibold text-gray-800">Data Wajib</h4>
                                <span class="text-xs text-red-500">* Wajib diisi</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Pegawai <span class="text-red-500">*</span></label>
                            <input type="text" name="nama" required 
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">NIP <span class="text-red-500">*</span></label>
                            <input type="text" name="nip" required 
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jabatan <span class="text-red-500">*</span></label>
                            <select name="jabatan_id" required 
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                <option value="">Pilih Jabatan</option>
                                @foreach($jabatanOptions as $j)
                                    <option value="{{ $j->id }}">{{ $j->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kantor Penempatan <span class="text-red-500">*</span></label>
                            <select name="kantor_id" required 
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                <option value="">Pilih Kantor</option>
                                @foreach($kantorOptions as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                            <select name="status" required 
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                <option value="aktif">Aktif</option>
                                <option value="keluar">Keluar</option>
                                <option value="mengundurkan_diri">Mengundurkan Diri</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Atasan Langsung</label>
                            <select name="atasan_langsung_id" id="atasan_langsung_id"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                <option value="">- Tidak Ada -</option>
                                @foreach($pegawaiOptions as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama }} ({{ $p->nip }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Data Tambahan -->
                        <div class="col-span-2 mt-2">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="h-6 w-1 bg-green-600 rounded"></div>
                                <h4 class="font-semibold text-gray-800">Data Tambahan</h4>
                                <span class="text-xs text-gray-500">(Opsional)</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" 
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Masuk</label>
                            <input type="date" name="tanggal_masuk" 
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                            <select name="jenis_kelamin" 
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                <option value="">Pilih</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status Pernikahan</label>
                            <input type="text" name="status_pernikahan" 
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                   placeholder="Menikah / Belum Menikah">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status Karyawan</label>
                            <input type="text" name="status_karyawan" 
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                   placeholder="Tetap / Kontrak">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pendidikan Terakhir</label>
                            <input type="text" name="pendidikan_terakhir" 
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                   placeholder="S1 / S2 / SMA">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Pasangan</label>
                            <input type="text" name="nama_pasangan" 
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jumlah Anak</label>
                            <input type="number" name="jumlah_anak" min="0" value="0"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">NIK</label>
                            <input type="text" name="nik" 
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                   placeholder="Nomor Induk Kependudukan">
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Alamat Tempat Tinggal</label>
                            <textarea name="alamat" rows="2" 
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                      placeholder="Alamat lengkap"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">No. HP</label>
                            <input type="text" name="no_hp" 
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                   placeholder="0812-3456-7890">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" 
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                   placeholder="email@domain.com">
                        </div>
                    </div>
                </form>
            </div>

            <div class="flex-shrink-0 px-6 py-4 border-t border-gray-200 flex justify-end space-x-2 bg-white rounded-b-lg">
                <button type="button" onclick="closeModal('tambahModal')" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-md transition">
                    <i class="fas fa-times mr-2"></i> Batal
                </button>
                <button type="submit" form="formTambahPegawai" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">
                    <i class="fas fa-save mr-2"></i> Simpan Pegawai
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ====== MODAL IMPORT ====== -->
<div id="importModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-white rounded-t-lg">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-file-import text-green-600 mr-2"></i>
                    Import Data Pegawai
                </h3>
                <button onclick="closeModal('importModal')" class="text-gray-400 hover:text-gray-600 text-2xl transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-6">
                <form action="{{ route('admin.pegawai.import') }}" method="POST" enctype="multipart/form-data" id="formImportPegawai">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">File Excel</label>
                            <input type="file" name="file" accept=".xlsx,.xls" required
                                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="mt-1 text-xs text-gray-500">Format: .xlsx, .xls | Max: 5MB</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.pegawai.template') }}" class="text-blue-600 hover:underline text-sm">
                                <i class="fas fa-download mr-1"></i> Download Template
                            </a>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end space-x-2">
                        <button type="button" onclick="closeModal('importModal')" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-md transition">
                            <i class="fas fa-times mr-2"></i> Batal
                        </button>
                        <button type="submit" form="formImportPegawai" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition">
                            <i class="fas fa-upload mr-2"></i> Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ====== MODAL BUAT AKUN ====== -->
<div id="createUserModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-white rounded-t-lg">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-user-plus text-blue-600 mr-2"></i>
                    Buat Akun Pegawai
                </h3>
                <button onclick="closeModal('createUserModal')" class="text-gray-400 hover:text-gray-600 text-2xl transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-6">
                <form action="" method="POST" id="formCreateUser">
                    @csrf
                    <input type="hidden" name="pegawai_id" id="create_user_pegawai_id">
                    
                    <div class="space-y-4">
                        <div class="bg-blue-50 rounded-lg p-3 mb-4 border border-blue-200">
                            <p class="text-sm text-gray-600">Membuat akun untuk:</p>
                            <p class="font-semibold text-gray-800" id="create_user_pegawai_nama">-</p>
                            <p class="text-xs text-gray-500" id="create_user_pegawai_nip">NIP: -</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Username <span class="text-red-500">*</span></label>
                            <input type="text" name="username" id="create_user_username" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                   placeholder="Masukkan username">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" id="create_user_email" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                   placeholder="email@domain.com">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Password <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="password" name="password" id="create_user_password" required minlength="8"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition pr-10"
                                       placeholder="Minimal 8 karakter">
                                <button type="button" onclick="togglePassword('create_user_password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Minimal 8 karakter</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Konfirmasi Password <span class="text-red-500">*</span></label>
                            <input type="password" name="password_confirmation" id="create_user_password_confirmation" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                   placeholder="Konfirmasi password">
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end space-x-2">
                        <button type="button" onclick="closeModal('createUserModal')" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-md transition">
                            <i class="fas fa-times mr-2"></i> Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">
                            <i class="fas fa-save mr-2"></i> Buat Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // ==================== OPEN / CLOSE MODAL ====================
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.body.style.overflow = '';
    }

    // ==================== BUAT AKUN ====================
    function openCreateUserModal(id, nama, nip) {
        document.getElementById('create_user_pegawai_id').value = id;
        document.getElementById('create_user_pegawai_nama').textContent = nama;
        document.getElementById('create_user_pegawai_nip').textContent = 'NIP: ' + nip;
        document.getElementById('formCreateUser').action = '/admin/pegawai/create-user/' + id;
        document.getElementById('formCreateUser').reset();
        openModal('createUserModal');
    }

    // ==================== TOGGLE PASSWORD ====================
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const icon = input.nextElementSibling.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target.classList.contains('fixed')) {
            document.querySelectorAll('.fixed').forEach(function(modal) {
                modal.classList.add('hidden');
            });
            document.body.style.overflow = '';
        }
    }

    // Close modal with ESC key
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