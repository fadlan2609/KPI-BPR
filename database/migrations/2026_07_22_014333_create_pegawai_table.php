<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 50)->unique();
            $table->string('nama', 100);
            $table->foreignId('jabatan_id')->constrained('jabatan');
            $table->foreignId('kantor_id')->constrained('kantor');
            $table->foreignId('atasan_langsung_id')->nullable()->constrained('pegawai')->onDelete('set null');
            $table->enum('status', ['aktif', 'keluar', 'mengundurkan_diri'])->default('aktif');
            $table->date('tanggal_lahir')->nullable();
            $table->date('tanggal_masuk')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('status_pernikahan', 20)->nullable();
            $table->string('status_karyawan', 50)->nullable();
            $table->string('pendidikan_terakhir', 50)->nullable();
            $table->string('nama_pasangan', 100)->nullable();
            $table->integer('jumlah_anak')->default(0);
            $table->string('nik', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};