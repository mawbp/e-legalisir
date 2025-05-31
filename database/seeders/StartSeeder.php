<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('users')->insert([
          [
            'name'       => 'user',
            'password'   => Hash::make('password123'),
            'email'      => 'user@gmail.com',
            'role'       => 'user',
            'created_at' => now(),
            'updated_at' => now(),
          ],
          [
            'name'       => 'admin',
            'password'   => Hash::make('password123'),
            'email'      => 'admin@gmail.com',
            'role'       => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
          ],
        ]);

        DB::table('dokumens')->insert([
          [
            'nama_dokumen'    => 'Ijazah',
            'deskripsi'       => '',
            'harga_per_lembar' => 3000,  
          ],
          [
            'nama_dokumen'    => 'Transkrip Nilai',
            'deskripsi'       => '',
            'harga_per_lembar' => 3000,
          ]
        ]);

        DB::table('status_permohonans')->insert([
          [
            'nama_status'   => 'Validasi Dokumen',
            'urutan'        => 1,
            'deskripsi'     => 'Mengecek kevalidan data dokumen',
          ],
          [
            'nama_status'   => 'Pengajuan ke Dekan',
            'urutan'        => 2,
            'deskripsi'     => 'Meminta tanda tangan kepada Dekan',
          ],
          [
            'nama status'   => 'Pengesahan Dokumen',
            'urutan'        => 3,
            'deskripsi'     => 'Pemberian stempel fakultas',
          ],
          [
            'nama status'   => 'Selesai',
            'urutan'        => 4,
            'deskripsi'     => 'Dokumen selesai dilegalisir',
          ],
        ]);
    }
}
