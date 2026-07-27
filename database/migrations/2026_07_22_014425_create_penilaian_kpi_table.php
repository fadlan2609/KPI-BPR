<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian_kpi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai');
            $table->foreignId('penilai_id')->constrained('pegawai');
            $table->foreignId('periode_id')->constrained('periode_penilaian');
            $table->enum('level_penilai', ['self', 'atasan_langsung', 'atasan_penilai']);
            $table->decimal('nilai_kpi', 5, 2);
            $table->decimal('nilai_kompetensi', 5, 2);
            $table->decimal('nilai_core_values', 5, 2);
            $table->decimal('nilai_total', 5, 2);
            $table->json('detail_indikator')->nullable();
            $table->enum('status', ['draft', 'submitted', 'final'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_kpi');
    }
};