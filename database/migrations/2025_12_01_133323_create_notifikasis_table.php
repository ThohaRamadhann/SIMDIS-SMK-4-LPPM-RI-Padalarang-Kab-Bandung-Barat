<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id('id_notifikasi');

            $table->unsignedBigInteger('id_pengguna');
            $table->unsignedBigInteger('id_pelanggaran');

            $table->text('isi_pesan');
            $table->string('jenis_notifikasi')->nullable(); // WA, Sistem, dll
            $table->timestamp('waktu_dikirim')->nullable();
            $table->string('status')->default('pending');
            $table->text('pesan_error')->nullable();

            $table->timestamps();

            $table->foreign('id_pengguna')
                ->references('id_pengguna')
                ->on('pengguna')
                ->cascadeOnDelete();

            $table->foreign('id_pelanggaran')
                ->references('id_pelanggaran')
                ->on('pelanggaran')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
