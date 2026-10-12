<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('riwayat_jabatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->onDelete('cascade');
            $table->foreignId('jabatan_id')->constrained('jabatan')->onDelete('restrict');
            $table->foreignId('kantor_id')->nullable()->constrained('kantor')->onDelete('set null');
            $table->integer('tahun_mulai');
            $table->integer('tahun_selesai')->nullable(); // null = masih menjabat
            $table->text('keterangan')->nullable();
            $table->enum('status', ['aktif', 'selesai'])->default('aktif');
            $table->timestamps();

            $table->index(['pegawai_id', 'tahun_mulai']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('riwayat_jabatan');
    }
};