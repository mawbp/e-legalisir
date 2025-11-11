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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable(false);
            $table->string('password', 255)->nullable(false);
            $table->string('email', 255)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('role', 10)->nullable();
            $table->string('reset_password_token', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
