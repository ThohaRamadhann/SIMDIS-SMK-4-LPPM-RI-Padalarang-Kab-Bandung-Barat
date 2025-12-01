<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id('id_kelas');

            $table->unsignedBigInteger('id_walikelas')->nullable();

            $table->string('tingkat');        // X / XI / XII
            $table->string('nama_kelas');     // IPA 1, IPS 2, dll
            $table->string('jurusan')->nullable(); // TKJ, RPL, dll
            $table->string('tahun_ajaran');   // 2024/2025

            $table->timestamps();

            $table->foreign('id_walikelas')
                ->references('id_walikelas')
                ->on('wali_kelas')
                ->nullOnDelete(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
