@extends('layouts.admin')

@section('title', 'Setting Bonus')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Setting Bonus</h1>
            <p class="text-sm text-gray-500">Periode: {{ $periode->nama }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.bonus.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md text-sm">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.bonus.save-setting') }}" method="POST">
        @csrf
        <input type="hidden" name="periode_id" value="{{ $periode->id }}">

        <div class="space-y-6">
            <!-- Total Bonus Pool -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Total Bonus Pool <span class="text-red-500">*</span></label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp</span>
                    <input type="number" name="total_pool" required
                           value="{{ $kebijakan->total_pool ?? '' }}"
                           min="0" step="1000"
                           class="pl-10 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="100000000">
                </div>
                <p class="mt-1 text-xs text-gray-500">Total anggaran bonus yang akan dibagikan</p>
            </div>

            <!-- Bobot Predikat -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Bobot Predikat</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($predikatList as $p)
                    <div class="p-4 border rounded-lg">
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold"
                                  style="color: {{ $p->warna_text }}; background-color: {{ $p->warna_latar }}">
                                {{ $p->nama }}
                            </span>
                            <input type="number" name="bobot[{{ $p->nama }}]" 
                                   value="{{ $kebijakan ? json_decode($kebijakan->bobot_predikat, true)[$p->nama] ?? 1 : 1 }}"
                                   min="0" max="10" step="0.1" required
                                   class="w-16 px-2 py-1 border border-gray-300 rounded-md text-center focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Nilai: {{ $p->batas_bawah }} - {{ $p->batas_atas }}</p>
                    </div>
                    @endforeach
                </div>
                <p class="mt-2 text-xs text-gray-500">Semakin tinggi bobot, semakin besar bonus yang diterima</p>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-2">
            <a href="{{ route('admin.bonus.index') }}" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-md">
                Batal
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                <i class="fas fa-save mr-2"></i> Simpan Setting
            </button>
        </div>
    </form>

    @if($kebijakan)
        <div class="mt-6 border-t pt-6">
            <form action="{{ route('admin.bonus.hitung') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="periode_id" value="{{ $periode->id }}">
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md"
                        onclick="return confirm('Hitung bonus untuk periode ini?')">
                    <i class="fas fa-calculator mr-2"></i> Hitung Bonus
                </button>
            </form>
            <form action="{{ route('admin.bonus.approve') }}" method="POST" class="inline ml-2">
                @csrf
                <input type="hidden" name="periode_id" value="{{ $periode->id }}">
                <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-md"
                        onclick="return confirm('Approve bonus untuk periode ini?')">
                    <i class="fas fa-check-double mr-2"></i> Approve Bonus
                </button>
            </form>
        </div>
    @endif
</div>
@endsection