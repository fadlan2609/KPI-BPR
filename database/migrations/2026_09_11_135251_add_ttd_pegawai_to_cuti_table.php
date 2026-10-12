<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cuti', function (Blueprint $table) {
            $table->string('ttd_pegawai')->nullable()->after('keterangan');
            $table->string('barcode_pegawai')->nullable()->after('ttd_pegawai');
        });
    }

    public function down()
    {
        Schema::table('cuti', function (Blueprint $table) {
            $table->dropColumn(['ttd_pegawai', 'barcode_pegawai']);
        });
    }
};