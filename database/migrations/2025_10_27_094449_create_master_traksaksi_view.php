<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(
            "CREATE VIEW master_transaksi_view AS 
            SELECT min(`p`.`created_at`) AS `tanggal_permohonan`, 
            count(`p`.`dokumen_id`) AS `jumlah_dokumen`, 
            sum(`p`.`jumlah_cetak`) AS `total_cetak`, 
            `bayar`.`jumlah_bayar` AS `jumlah_bayar`, 
            `bayar`.`metode_pembayaran` AS `metode_pembayaran`, 
            `bayar`.`metode_pengiriman` AS `metode_pengiriman`, 
            `bayar`.`biaya_kurir` AS `biaya_kurir`, 
            `bayar`.`status_pembayaran` AS `status_pembayaran`, 
            `p`.`permohonan_id` AS `permohonan_id`, 
            `alumni`.`nim` AS `nim`, 
            `alumni`.`nama` AS `nama`, 
            `alumni`.`prodi` AS `prodi`
            FROM 
            (
                (
                    (
                        `permohonan` `p` 
                        join `pembayaran` `bayar` on((
                            `p`.`pembayaran_id` = `bayar`.`id`
                        ))
                    )
                join `users` `u` on((
                    `p`.`user_id` = `u`.`id`
                    ))
                ) 
            join `alumni` on((
                `u`.`alumni_id` = `alumni`.`id`
                ))
            ) 
            GROUP BY 
            `bayar`.`jumlah_bayar`, 
            `bayar`.`metode_pembayaran`, 
            `bayar`.`metode_pengiriman`, 
            `bayar`.`biaya_kurir`, 
            `bayar`.`status_pembayaran`, 
            `p`.`permohonan_id`, 
            `alumni`.`nim`, 
            `alumni`.`nama`, 
            `alumni`.`prodi`"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS master_transaksi_view");
    }
};
