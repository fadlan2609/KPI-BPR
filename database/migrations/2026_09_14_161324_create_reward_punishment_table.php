<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reward_punishment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->onDelete('cascade');

            // Kategori: reward / punishment
            $table->enum('kategori', ['reward', 'punishment']);

            // Tipe
            $table->string('tipe', 50);

            // Detail
            $table->string('judul');
            $table->text('keterangan')->nullable();
            $table->date('tanggal');

            // Nominal (opsional)
            $table->decimal('nominal', 15, 2)->nullable();

            // File bukti
            $table->string('file_bukti')->nullable();

            // Status
            $table->enum('status', ['aktif', 'selesai', 'dibatalkan'])->default('aktif');

            // Tracking
            $table->foreignId('created_by')->nullable()->constrained('pegawai')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('pegawai')->onDelete('set null');

            $table->softDeletes();
            $table->timestamps();

            // Index
            $table->index(['pegawai_id', 'kategori']);
            $table->index('tanggal');
            $table->index('status');
            $table->index('tipe');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reward_punishment');
    }
};