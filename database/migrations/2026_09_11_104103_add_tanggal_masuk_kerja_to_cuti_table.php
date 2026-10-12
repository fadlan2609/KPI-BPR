<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cuti', function (Blueprint $table) {
            $table->date('tanggal_masuk_kerja')->nullable()->after('tanggal_selesai');
        });
    }

    public function down()
    {
        Schema::table('cuti', function (Blueprint $table) {
            $table->dropColumn('tanggal_masuk_kerja');
        });
    }
};