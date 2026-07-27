<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perhitungan_bonus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai');
            $table->foreignId('periode_id')->constrained('periode_penilaian');
            $table->decimal('gaji_pokok', 15, 2);
            $table->decimal('nilai_kinerja', 5, 2);
            $table->string('predikat', 50);
            $table->decimal('bobot', 5, 2);
            $table->decimal('bonus_dasar', 15, 2);
            $table->decimal('bonus_akhir', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perhitungan_bonus');
    }
};