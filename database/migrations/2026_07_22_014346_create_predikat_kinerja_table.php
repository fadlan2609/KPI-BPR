<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('predikat_kinerja', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50);
            $table->text('keterangan')->nullable();
            $table->decimal('batas_atas', 5, 2);
            $table->decimal('batas_bawah', 5, 2);
            $table->string('warna_text', 7)->default('#000000');
            $table->string('warna_latar', 7)->default('#FFFFFF');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('predikat_kinerja');
    }
};