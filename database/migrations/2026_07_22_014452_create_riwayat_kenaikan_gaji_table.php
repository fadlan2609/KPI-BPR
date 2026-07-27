<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_kenaikan_gaji', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai');
            $table->foreignId('periode_id')->constrained('periode_penilaian');
            $table->decimal('gaji_lama', 15, 2);
            $table->decimal('gaji_baru', 15, 2);
            $table->decimal('persentase', 5, 2);
            $table->string('predikat', 50);
            $table->date('tanggal_efektif');
            $table->foreignId('approved_by')->constrained('pegawai');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_kenaikan_gaji');
    }
};