@extends('layouts.admin')

@section('title', 'Jabatan Pegawai')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Daftar Jabatan Pegawai</h1>
        <div class="flex space-x-2">
            <button onclick="openModal('importModal')" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm">
                <i class="fas fa-file-import mr-2"></i> Import
            </button>
            <button onclick="openModal('tambahModal')" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">
                <i class="fas fa-plus mr-2"></i> Tambah Jabatan
            </button>
        </div>
    </div>
    
    <!-- Filter & Search -->
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex flex-wrap gap-4">
            <input type="text" id="search" placeholder="Cari jabatan..." 
                   class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 flex-1">
            <button onclick="filterData()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                <i class="fas fa-search mr-2"></i> Cari
            </button>
            <button onclick="resetFilter()" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md">
                <i class="fas fa-undo mr-2"></i> Reset
            </button>
        </div>
    </div>
    
    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Jabatan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jabatan Atasan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($jabatan as $index => $j)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $j->nama }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $j->atasan->nama ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $j->keterangan ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('admin.jabatan.edit', $j->id) }}" class="text-blue-600 hover:text-blue-900 mr-2">
                            <i class="fas fa-edit"></i>
                        </a>
                        {{-- FORM DELETE dengan parameter --}}
                        <form action="{{ route('admin.jabatan.destroy', $j->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus jabatan ini?')">
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
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada data jabatan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-3">
            {{ $jabatan->links() }}
        </div>
    </div>
</div>

<!-- ====== MODAL TAMBAH JABATAN ====== -->
<div id="tambahModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Tambah Jabatan Baru</h3>
            <button onclick="closeModal('tambahModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form action="{{ route('admin.jabatan.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Jabatan*</label>
                    <input type="text" name="nama" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Jabatan Atasan</label>
                    <select name="jabatan_atasan_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">- Kosong -</option>
                        @foreach($jabatanOptions as $j)
                            <option value="{{ $j->id }}">{{ $j->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Keterangan</label>
                    <textarea name="keterangan" rows="2" 
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-2">
                <button type="button" onclick="closeModal('tambahModal')" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-md">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                    <i class="fas fa-save mr-2"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ====== MODAL IMPORT ====== -->
<div id="importModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Import Data Jabatan</h3>
            <button onclick="closeModal('importModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form action="{{ route('admin.jabatan.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">File Excel</label>
                    <input type="file" name="file" accept=".xlsx,.xls" required
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="mt-1 text-xs text-gray-500">Format: .xlsx, .xls | Max: 5MB</p>
                </div>
                <div>
                    <a href="{{ route('admin.jabatan.template') }}" class="text-blue-600 hover:underline text-sm">
                        <i class="fas fa-download mr-1"></i> Download Template
                    </a>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-2">
                <button type="button" onclick="closeModal('importModal')" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-md">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md">
                    <i class="fas fa-upload mr-2"></i> Import
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.getElementById(id).classList.add('flex');
    }
    
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.getElementById(id).classList.remove('flex');
    }
    
    function filterData() {
        const search = document.getElementById('search').value;
        window.location.href = '{{ route("admin.jabatan.index") }}?search=' + search;
    }
    
    function resetFilter() {
        document.getElementById('search').value = '';
        window.location.href = '{{ route("admin.jabatan.index") }}';
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target.classList.contains('fixed')) {
            event.target.classList.add('hidden');
            event.target.classList.remove('flex');
        }
    }
</script>
@endpush
@endsection