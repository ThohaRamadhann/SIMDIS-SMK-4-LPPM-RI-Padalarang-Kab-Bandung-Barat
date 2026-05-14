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

            // Untuk tracking job Laravel Queue — dipakai sistem grace period EWS
            $table->string('job_id')->nullable();

            $table->text('isi_pesan');
            $table->string('jenis_notifikasi')->nullable();
            $table->timestamp('waktu_dikirim')->nullable();

            // pending   → notif dibuat, job belum jalan (dalam grace period)
            // terkirim  → job sudah jalan, notif muncul di inbox penerima
            // dibatalkan → pelanggaran diedit/dihapus sebelum grace period habis
            // gagal     → job error setelah semua tries habis
            $table->enum('status', ['pending', 'terkirim', 'dibatalkan', 'gagal'])
                  ->default('pending');

            $table->text('pesan_error')->nullable();

            // Untuk fitur baca/belum baca
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();

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