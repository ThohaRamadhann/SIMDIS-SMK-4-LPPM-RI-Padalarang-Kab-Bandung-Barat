<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pelanggaran', function (Blueprint $table) {
            $table->id('id_pelanggaran');

            $table->unsignedBigInteger('id_siswa');
            $table->unsignedBigInteger('id_walikelas');
            $table->unsignedBigInteger('id_jenispelanggaran');

            $table->timestamp('waktu_kejadian')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('status_pembinaan')->default('belum ditindak');

            $table->timestamps();

            $table->foreign('id_siswa')
                ->references('id_siswa')
                ->on('siswa')
                ->cascadeOnDelete();

            $table->foreign('id_walikelas')
                ->references('id_walikelas')
                ->on('wali_kelas')
                ->cascadeOnDelete();

            $table->foreign('id_jenispelanggaran')
                ->references('id_jenispelanggaran')
                ->on('jenis_pelanggaran')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelanggaran');
    }
};
