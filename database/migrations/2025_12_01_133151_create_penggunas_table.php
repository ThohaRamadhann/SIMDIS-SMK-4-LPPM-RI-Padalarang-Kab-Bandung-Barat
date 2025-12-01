<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pengguna', function (Blueprint $table) {
            $table->id('id_pengguna');

            $table->unsignedBigInteger('id_role');
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->nullable();
            $table->string('no_telpon')->nullable();
            $table->string('password');

            $table->timestamps();

            $table->foreign('id_role')
                ->references('id_role')
                ->on('role')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};
