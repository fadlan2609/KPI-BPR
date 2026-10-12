<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cuti_approvals', function (Blueprint $table) {
            // Flag apakah approver ini adalah FINAL approver
            $table->boolean('is_final')->default(false)->after('status');
            
            // Flag apakah approver ini sudah ditentukan dari awal
            $table->boolean('is_planned')->default(true)->after('is_final');
        });
    }

    public function down()
    {
        Schema::table('cuti_approvals', function (Blueprint $table) {
            $table->dropColumn(['is_final', 'is_planned']);
        });
    }
};