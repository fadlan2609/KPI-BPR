<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cuti_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cuti_id')->constrained('cuti')->onDelete('cascade');
            $table->integer('level');
            $table->foreignId('approver_id')->constrained('pegawai');
            $table->foreignId('requested_by')->constrained('pegawai');
            $table->enum('status', ['pending', 'disetujui', 'ditolak', 'dilewati'])->default('pending');
            $table->text('catatan')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['cuti_id', 'level']);
            $table->index(['approver_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('cuti_approvals');
    }
};