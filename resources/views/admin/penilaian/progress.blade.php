@extends('layouts.admin')

@section('title', 'Progress Penilaian KPI')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Memulai Penilaian Kinerja Pegawai</h1>
        @if($periode)
            <span class="text-sm text-gray-500">Periode: {{ $periode->nama }}</span>
        @endif
    </div>

    @if(!$periode)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-yellow-800">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                {{ $message ?? 'Belum ada periode penilaian aktif' }}
            </p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($progress as $key => $item)
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-md transition">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                @if($item['completed'])
                                    <span class="flex items-center justify-center w-8 h-8 bg-green-500 text-white rounded-full">
                                        <i class="fas fa-check"></i>
                                    </span>
                                @else
                                    <span class="flex items-center justify-center w-8 h-8 bg-gray-300 text-gray-600 rounded-full">
                                        {{ $loop->iteration }}
                                    </span>
                                @endif
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ $item['label'] }}
                                </h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ $item['description'] }}
                                </p>
                                @if(isset($item['last_updated']) && $item['last_updated'] != '-')
                                    <p class="text-xs text-gray-400 mt-1">
                                        <i class="fas fa-clock mr-1"></i>
                                        Terakhir diperbarui: {{ $item['last_updated'] }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex-shrink-0 ml-4">
                        @if($item['completed'])
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                                Selesai
                            </span>
                        @else
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">
                                Belum
                            </span>
                        @endif
                    </div>
                </div>
                <div class="mt-4 flex justify-end">
                    <a href="{{ $item['button_url'] }}" 
                       class="px-4 py-2 {{ $item['completed'] ? 'bg-green-600 hover:bg-green-700' : 'bg-blue-600 hover:bg-blue-700' }} text-white rounded-md text-sm transition">
                        <i class="fas {{ $item['completed'] ? 'fa-check-circle' : 'fa-arrow-right' }} mr-2"></i>
                        {{ $item['button_text'] }}
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Summary Progress -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Ringkasan Progress</h3>
            @php
                $total = count($progress);
                $completed = collect($progress)->filter(function($item) {
                    return $item['completed'];
                })->count();
                $percentage = $total > 0 ? round(($completed / $total) * 100) : 0;
            @endphp
            <div class="flex items-center">
                <div class="flex-1">
                    <div class="relative pt-1">
                        <div class="flex mb-2 items-center justify-between">
                            <div>
                                <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-blue-600 bg-blue-200">
                                    Progress
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-semibold inline-block text-blue-600">
                                    {{ $percentage }}%
                                </span>
                            </div>
                        </div>
                        <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-blue-200">
                            <div style="width:{{ $percentage }}%" 
                                 class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500 transition-all duration-500">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ml-4 text-center">
                    <p class="text-2xl font-bold text-gray-800">{{ $completed }}/{{ $total }}</p>
                    <p class="text-xs text-gray-500">Langkah Selesai</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection