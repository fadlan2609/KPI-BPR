<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('saldo_cuti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->onDelete('cascade');
            $table->integer('tahun');
            $table->integer('total_hari')->default(12);
            $table->integer('digunakan')->default(0);
            $table->integer('sisa_hari')->default(12);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['pegawai_id', 'tahun']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('saldo_cuti');
    }
};