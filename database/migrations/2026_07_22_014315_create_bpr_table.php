<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bpr', function (Blueprint $table) {
            $table->id();
            $table->string('sandi_bpr', 50)->unique();
            $table->string('nama_bpr', 100);
            $table->string('jenis_bpr', 50)->nullable();
            $table->string('jenis_lembaga', 50)->nullable();
            $table->string('kategori', 50)->nullable();
            $table->string('no_telp', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->string('email', 100)->nullable();
            $table->string('website', 100)->nullable();
            $table->string('logo', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bpr');
    }
};