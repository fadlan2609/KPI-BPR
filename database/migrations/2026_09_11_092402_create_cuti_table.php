<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cuti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->onDelete('cascade');
            $table->enum('jenis_cuti', ['tahunan', 'sakit', 'melahirkan', 'khusus', 'lainnya']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('lama_hari')->default(0);
            $table->text('keterangan')->nullable();
            $table->enum('status', ['pending', 'proses', 'disetujui', 'ditolak', 'dibatalkan'])->default('pending');
            $table->integer('current_approval_level')->default(1);
            $table->foreignId('final_approved_by')->nullable()->constrained('pegawai');
            $table->timestamp('final_approved_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index(['pegawai_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('cuti');
    }
};