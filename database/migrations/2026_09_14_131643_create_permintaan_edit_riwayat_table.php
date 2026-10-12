<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('permintaan_edit_riwayat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->onDelete('cascade');
            $table->foreignId('riwayat_jabatan_id')->nullable()->constrained('riwayat_jabatan')->onDelete('cascade');
            $table->enum('jenis_permintaan', ['tambah', 'edit', 'hapus'])->default('edit');
            $table->text('alasan')->nullable();
            $table->json('data_baru')->nullable(); // data yang mau diubah
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('pegawai');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['pegawai_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('permintaan_edit_riwayat');
    }
};