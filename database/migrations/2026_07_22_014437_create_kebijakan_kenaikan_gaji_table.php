<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kebijakan_kenaikan_gaji', function (Blueprint $table) {
            $table->id();
            $table->foreignId('predikat_id')->constrained('predikat_kinerja');
            $table->decimal('persentase', 5, 2);
            $table->decimal('minimal_gaji', 15, 2)->nullable();
            $table->decimal('maksimal_gaji', 15, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kebijakan_kenaikan_gaji');
    }
};