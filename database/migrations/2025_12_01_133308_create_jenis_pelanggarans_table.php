<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('jenis_pelanggaran', function (Blueprint $table) {
            $table->id('id_jenispelanggaran');
            $table->string('nama_pelanggaran');
            $table->enum('tingkat_pelanggaran', ['ringan', 'sedang', 'berat']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_pelanggaran');
    }
};
