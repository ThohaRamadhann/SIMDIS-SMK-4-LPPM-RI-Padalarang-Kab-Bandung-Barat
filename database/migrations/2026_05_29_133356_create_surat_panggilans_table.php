<?php
// database/migrations/xxxx_create_surat_panggilan_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('surat_panggilan', function (Blueprint $table) {
            $table->id('id_surat');
            $table->unsignedBigInteger('id_pelanggaran');
            $table->unsignedBigInteger('id_walikelas');
            $table->string('nomor_surat')->unique();
            $table->date('tanggal_panggilan');
            $table->time('waktu_panggilan');
            $table->string('tempat')->default('SMK 4 LPPM RI Padalarang');
            $table->timestamps();

            $table->foreign('id_pelanggaran')
                ->references('id_pelanggaran')
                ->on('pelanggaran')
                ->cascadeOnDelete();

            $table->foreign('id_walikelas')
                ->references('id_walikelas')
                ->on('wali_kelas')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_panggilan');
    }
};