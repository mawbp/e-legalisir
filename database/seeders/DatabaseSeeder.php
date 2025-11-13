<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $password = 'admin123';
        $hash = Hash::make($password);
        DB::table('users')->insert([
            'id' => 1,
            'name' => 'admin',
            'password' => $hash,
            'email' => 'admin@gmail.com',
            'phone' => '09120312301',
            'role' => 'admin'
        ]);

        DB::table('pengaturan')->insert([
            [
                'nama' => 'nama_fakultas',
                'nilai' => 'Fakultas Teknologi Informasi dan Sains'
            ],
            [
                'nama' => 'nama_kampus',
                'nilai' => 'Universitas Pembangunan Jaya'
            ],
            [
                'nama' => 'nama_bank',
                'nilai' => 'Bank Central Asia (BCA)'
            ],
            [
                'nama' => 'no_rekening',
                'nilai' => '0123456789'
            ],
            [
                'nama' => 'maksimal_cetak',
                'nilai' => '10'
            ],
            [
                'nama' => 'biaya_admin',
                'nilai' => '2000'
            ],
        ]);
    }
}
