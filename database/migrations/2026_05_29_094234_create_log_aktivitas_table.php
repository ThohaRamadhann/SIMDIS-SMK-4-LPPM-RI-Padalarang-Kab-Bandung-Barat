<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('log_aktivitas', function (Blueprint $table) {
            $table->id('id_log');

            $table->unsignedBigInteger('id_pengguna');

            $table->string('aksi');        // login, logout, tambah_pelanggaran, dll
            $table->string('modul');       // pelanggaran, jenis_pelanggaran, siswa, dll
            $table->text('keterangan')->nullable(); // detail aktivitas
            $table->unsignedBigInteger('id_referensi')->nullable(); // id data yang terkait misal id_pelanggaran
            $table->string('ip_address')->nullable();

            $table->timestamp('waktu')->useCurrent();

            $table->foreign('id_pengguna')
                ->references('id_pengguna')
                ->on('pengguna')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_aktivitas');
    }
};