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
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->string('metode_pembayaran', 255)->nullable();
            $table->string('metode_pengiriman', 255)->nullable(false);
            $table->string('biaya_kurir', 255)->nullable();
            $table->string('jumlah_bayar', 255)->nullable(false);
            $table->unsignedBigInteger('alamat_id')->nullable(false);
            $table->string('status_pembayaran', 255)->nullable(false);
            $table->string('bukti_pembayaran', 255)->nullable();
            $table->dateTime('expired_at')->nullable();
            $table->string('snap_token', 255)->nullable();
            $table->timestamps();

            $table->foreign('alamat_id')->references('id')->on('alamat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
