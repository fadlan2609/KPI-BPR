<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cuti', function (Blueprint $table) {
            // Path file surat dokter
            $table->string('surat_dokter')->nullable()->after('keterangan');

            // Status: null (belum perlu), 'menunggu' (wajib tapi belum upload), 'sudah' (sudah upload)
            $table->enum('surat_dokter_status', ['menunggu', 'sudah'])
                  ->nullable()
                  ->after('surat_dokter');
        });
    }

    public function down()
    {
        Schema::table('cuti', function (Blueprint $table) {
            $table->dropColumn(['surat_dokter', 'surat_dokter_status']);
        });
    }
};