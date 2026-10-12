<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pegawai', function (Blueprint $table) {
            // Jenis TTD: 'gambar' atau 'barcode' (nullable kalau belum upload)
            $table->enum('ttd_type', ['gambar', 'barcode'])->nullable()->after('email');

            // Path file TTD
            $table->string('ttd_image')->nullable()->after('ttd_type');
        });
    }

    public function down()
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->dropColumn(['ttd_type', 'ttd_image']);
        });
    }
};