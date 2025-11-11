<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('registrasi', function (Blueprint $table) {
            $table->id();
            $table->string('nim', 10)->nullable(false);
            $table->string('nama', 100)->nullable(false);
            $table->string('email', 100)->nullable(false);
            $table->string('nomor_ijazah', 255)->nullable(false);
            $table->string('status', 50)->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrasi');
    }
};
