<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('keluarga_pendidikan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('keluarga_id')->constrained('pegawai_keluarga')->onDelete('cascade');
            $table->enum('jenjang', ['SD', 'SMP', 'SMA/SMK', 'D3', 'S1', 'S2', 'S3'])->default('S1');
            $table->string('nama_sekolah');
            $table->string('jurusan')->nullable();
            $table->string('kota')->nullable();
            $table->year('tahun_masuk')->nullable();
            $table->year('tahun_lulus')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('keluarga_pendidikan');
    }
};