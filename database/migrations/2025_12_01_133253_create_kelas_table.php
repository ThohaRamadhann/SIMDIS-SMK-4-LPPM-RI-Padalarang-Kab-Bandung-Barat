<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id('id_kelas');

            // Satu wali kelas hanya boleh mengampu satu kelas
            $table->unsignedBigInteger('id_walikelas')
                ->nullable()
                ->unique();

            $table->string('tingkat');        // X, XI, XII
            $table->string('nama_kelas');     // RPL 1, TKJ 2, dll
            $table->string('jurusan')->nullable();
            $table->string('tahun_ajaran');

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