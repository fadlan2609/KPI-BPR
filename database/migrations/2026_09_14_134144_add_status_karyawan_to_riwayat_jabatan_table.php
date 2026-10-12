<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('riwayat_jabatan', function (Blueprint $table) {
            $table->enum('status_karyawan', [
                'magang',
                'kontrak',
                'tetap',
                'honorer',
                'outsourcing',
                'lainnya'
            ])->nullable()->after('kantor_id');

            $table->string('jenis_kontrak')->nullable()->after('status_karyawan');
        });
    }

    public function down()
    {
        Schema::table('riwayat_jabatan', function (Blueprint $table) {
            $table->dropColumn(['status_karyawan', 'jenis_kontrak']);
        });
    }
};