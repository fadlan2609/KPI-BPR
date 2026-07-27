<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai');
            $table->foreignId('periode_id')->constrained('periode_penilaian');
            $table->foreignId('predikat_id')->nullable()->constrained('predikat_kinerja');
            $table->decimal('nilai_self', 5, 2)->nullable();
            $table->decimal('nilai_atasan_langsung', 5, 2)->nullable();
            $table->decimal('nilai_atasan_penilai', 5, 2)->nullable();
            $table->decimal('nilai_akhir', 5, 2);
            $table->enum('status', ['draft', 'final'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('pegawai');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_penilaian');
    }
};