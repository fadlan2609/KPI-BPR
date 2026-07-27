<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bobot_penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jabatan_id')->constrained('jabatan');
            $table->foreignId('periode_id')->constrained('periode_penilaian');
            $table->decimal('bobot_kpi', 5, 2)->default(70);
            $table->decimal('bobot_kompetensi', 5, 2)->default(15);
            $table->decimal('bobot_core_values', 5, 2)->default(15);
            $table->decimal('bobot_self', 5, 2)->default(20);
            $table->decimal('bobot_atasan_langsung', 5, 2)->default(50);
            $table->decimal('bobot_atasan_penilai', 5, 2)->default(30);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bobot_penilaian');
    }
};