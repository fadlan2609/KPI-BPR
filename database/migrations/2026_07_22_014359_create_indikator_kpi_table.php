<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indikator_kpi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jabatan_id')->constrained('jabatan')->onDelete('cascade');
            $table->text('indikator');
            $table->decimal('target', 15, 2);
            $table->string('satuan', 50);
            $table->decimal('bobot', 5, 2);
            $table->enum('periode', ['bulanan', 'tahunan'])->default('bulanan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indikator_kpi');
    }
};