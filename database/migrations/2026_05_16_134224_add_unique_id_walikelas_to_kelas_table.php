<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            // Ubah dulu jadi nullable agar kelas bisa dibuat tanpa wali kelas
            $table->unsignedBigInteger('id_walikelas')->nullable()->change();

            // Unique: 1 wali kelas hanya bisa mengampu 1 kelas
            $table->unique('id_walikelas', 'unique_walikelas');
        });
    }

    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropUnique('unique_walikelas');
            $table->unsignedBigInteger('id_walikelas')->nullable(false)->change();
        });
    }
};
