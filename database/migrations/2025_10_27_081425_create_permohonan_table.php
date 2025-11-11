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
        Schema::create('permohonan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(false);
            $table->string('permohonan_id', 255)->nullable(false);
            $table->integer('jumlah_cetak')->nullable(false);
            $table->string('harga_per_lembar', 10)->nullable();
            $table->unsignedBigInteger('dokumen_id')->nullable(false);
            $table->string('status_permohonan', 50)->nullable(false);
            $table->unsignedBigInteger('pembayaran_id')->nullable();
            $table->string('catatan', 255)->nullable();
            $table->string('kurir', 20)->nullable();
            $table->string('tipe_kurir', 20)->nullable();
            $table->string('order_id', 100)->nullable();
            $table->string('tracking_id', 100)->nullable();
            $table->string('waybill_id', 100)->nullable();
            $table->string('catatan_pengiriman', 255)->nullable();
            $table->string('metode_pengambilan_terpilih', 10)->nullable();
            $table->string('metode_pengambilan', 255)->nullable();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('dokumen_id')->references('id')->on('dokumen');
            $table->foreign('pembayaran_id')->references('id')->on('pembayaran');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonan');
    }
};
