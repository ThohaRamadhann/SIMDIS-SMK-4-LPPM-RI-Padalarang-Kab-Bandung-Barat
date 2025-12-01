<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('siswa', function (Blueprint $table) {
            $table->id('id_siswa');

            $table->unsignedBigInteger('id_walimurid');
            $table->unsignedBigInteger('id_kelas');

            $table->string('nama');
            $table->string('nis')->unique();
            $table->string('status')->default('aktif');

            $table->timestamps();

            $table->foreign('id_walimurid')
                ->references('id_walimurid')
                ->on('wali_murid')
                ->cascadeOnDelete();

            $table->foreign('id_kelas')
                ->references('id_kelas')
                ->on('kelas')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};
