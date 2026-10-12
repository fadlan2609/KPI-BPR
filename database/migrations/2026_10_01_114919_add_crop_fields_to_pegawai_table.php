<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pegawai', function (Blueprint $table) {
            // Koordinat crop (persentase dari gambar asli)
            $table->decimal('foto_crop_x', 5, 4)->nullable()->after('foto_profil');
            $table->decimal('foto_crop_y', 5, 4)->nullable()->after('foto_crop_x');
            $table->decimal('foto_crop_zoom', 5, 4)->default(1)->after('foto_crop_y');
            $table->integer('foto_crop_rotate')->default(0)->after('foto_crop_zoom');
        });
    }

    public function down()
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->dropColumn(['foto_crop_x', 'foto_crop_y', 'foto_crop_zoom', 'foto_crop_rotate']);
        });
    }
};