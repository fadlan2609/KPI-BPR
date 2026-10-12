<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bpr', function (Blueprint $table) {
            $table->string('ttd_image')->nullable()->after('logo');      // gambar tanda tangan
            $table->string('ttd_nama')->nullable()->after('ttd_image');  // nama penandatangan
            $table->string('ttd_jabatan')->nullable()->after('ttd_nama');// jabatan penandatangan
            $table->string('barcode_ttd')->nullable()->after('ttd_jabatan'); // barcode TTD
        });
    }

    public function down()
    {
        Schema::table('bpr', function (Blueprint $table) {
            $table->dropColumn(['ttd_image', 'ttd_nama', 'ttd_jabatan', 'barcode_ttd']);
        });
    }
};