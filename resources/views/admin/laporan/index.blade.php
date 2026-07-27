@extends('layouts.admin')

@section('title', 'Laporan')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Laporan</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Laporan Penilaian -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-file-alt text-blue-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Laporan Penilaian</h3>
                    <p class="text-sm text-gray-500">Laporan hasil penilaian kinerja pegawai</p>
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <a href="{{ route('admin.laporan.penilaian') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">
                    <i class="fas fa-arrow-right mr-2"></i> Lihat
                </a>
            </div>
        </div>

        <!-- Laporan Gaji -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-money-bill-wave text-green-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Laporan Gaji</h3>
                    <p class="text-sm text-gray-500">Laporan kenaikan gaji pegawai</p>
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <a href="{{ route('admin.laporan.gaji') }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm">
                    <i class="fas fa-arrow-right mr-2"></i> Lihat
                </a>
            </div>
        </div>

        <!-- Laporan Bonus -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-gift text-yellow-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Laporan Bonus</h3>
                    <p class="text-sm text-gray-500">Laporan pembagian bonus pegawai</p>
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <a href="{{ route('admin.laporan.bonus') }}" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-md text-sm">
                    <i class="fas fa-arrow-right mr-2"></i> Lihat
                </a>
            </div>
        </div>

        <!-- Laporan Cuti -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-calendar-alt text-purple-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Laporan Cuti</h3>
                    <p class="text-sm text-gray-500">Laporan cuti pegawai</p>
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <a href="{{ route('admin.laporan.cuti') }}" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-md text-sm">
                    <i class="fas fa-arrow-right mr-2"></i> Lihat
                </a>
            </div>
        </div>
    </div>
</div>
@endsection