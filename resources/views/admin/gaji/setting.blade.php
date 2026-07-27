@extends('layouts.admin')

@section('title', 'Setting Kenaikan Gaji')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Setting Kenaikan Gaji</h1>
        <a href="{{ route('admin.gaji.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md text-sm">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <form action="{{ route('admin.gaji.save-setting') }}" method="POST">
        @csrf
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Predikat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Persentase Kenaikan (%)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Minimal Gaji</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Maksimal Gaji</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($predikat as $p)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold"
                                  style="color: {{ $p->warna_text }}; background-color: {{ $p->warna_latar }}">
                                {{ $p->nama }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="number" name="persentase[{{ $p->id }}]" 
                                   value="{{ $kebijakan[$p->id]->persentase ?? 0 }}"
                                   min="0" max="100" step="0.5" required
                                   class="w-24 px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="number" name="minimal_gaji[{{ $p->id }}]" 
                                   value="{{ $kebijakan[$p->id]->minimal_gaji ?? '' }}"
                                   min="0" step="1000"
                                   class="w-32 px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Contoh: 4000000">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="number" name="maksimal_gaji[{{ $p->id }}]" 
                                   value="{{ $kebijakan[$p->id]->maksimal_gaji ?? '' }}"
                                   min="0" step="1000"
                                   class="w-32 px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Contoh: 15000000">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                <i class="fas fa-save mr-2"></i> Simpan Setting
            </button>
        </div>
    </form>
</div>
@endsection