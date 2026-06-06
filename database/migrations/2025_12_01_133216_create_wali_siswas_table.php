<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('wali_siswa', function (Blueprint $table) {
            $table->id('id_walisiswa');
            $table->unsignedBigInteger('id_pengguna');
            $table->string('hubungan');

            $table->timestamps();
            $table->softDeletes(); // menambahkan kolom deleted_at

            $table->foreign('id_pengguna')
                ->references('id_pengguna')
                ->on('pengguna')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wali_siswa');
    }
};