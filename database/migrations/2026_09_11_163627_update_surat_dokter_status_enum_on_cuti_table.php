<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Ubah enum: tambahkan 'disetujui' dan 'ditolak'
        DB::statement("
            ALTER TABLE `cuti` 
            MODIFY COLUMN `surat_dokter_status` 
            ENUM('menunggu', 'disetujui', 'ditolak') 
            NULL DEFAULT NULL
        ");
    }

    public function down()
    {
        DB::statement("
            ALTER TABLE `cuti` 
            MODIFY COLUMN `surat_dokter_status` 
            ENUM('menunggu', 'sudah') 
            NULL DEFAULT NULL
        ");
    }
};