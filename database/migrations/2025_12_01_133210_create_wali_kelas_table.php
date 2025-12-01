<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('wali_kelas', function (Blueprint $table) {
            $table->id('id_walikelas');

            $table->unsignedBigInteger('id_pengguna');
            $table->string('nuptk')->nullable();
            $table->string('jabatan')->nullable(); // walikelas / guru bk

            $table->timestamps();

            $table->foreign('id_pengguna')
                ->references('id_pengguna')
                ->on('pengguna')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wali_kelas');
    }
};
