<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('indikator_kpi', function (Blueprint $table) {
            $table->string('perspektif')->nullable()->after('jabatan_id');
            $table->string('nama')->nullable()->after('perspektif');
        });

        Schema::table('indikator_kompetensi', function (Blueprint $table) {
            $table->string('perspektif')->nullable()->after('jabatan_id');
            $table->string('nama')->nullable()->after('perspektif');
        });

        Schema::table('indikator_core_values', function (Blueprint $table) {
            $table->string('perspektif')->nullable()->after('jabatan_id');
            $table->string('nama')->nullable()->after('perspektif');
        });
    }

    public function down(): void
    {
        Schema::table('indikator_kpi', function (Blueprint $table) {
            $table->dropColumn(['perspektif', 'nama']);
        });

        Schema::table('indikator_kompetensi', function (Blueprint $table) {
            $table->dropColumn(['perspektif', 'nama']);
        });

        Schema::table('indikator_core_values', function (Blueprint $table) {
            $table->dropColumn(['perspektif', 'nama']);
        });
    }
};