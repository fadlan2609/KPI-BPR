<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Ubah ENUM status: tambahkan 'menunggu'
        DB::statement("
            ALTER TABLE `cuti_approvals` 
            MODIFY COLUMN `status` 
            ENUM('pending', 'menunggu', 'disetujui', 'ditolak', 'dilewati') 
            NOT NULL DEFAULT 'menunggu'
        ");
    }

    public function down()
    {
        // Balikkan ke ENUM semula
        DB::statement("
            ALTER TABLE `cuti_approvals` 
            MODIFY COLUMN `status` 
            ENUM('pending', 'disetujui', 'ditolak', 'dilewati') 
            NOT NULL DEFAULT 'pending'
        ");
    }
};