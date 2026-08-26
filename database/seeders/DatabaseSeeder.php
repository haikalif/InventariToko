<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ambil data dari file .env (lebih aman)
        // Jika di .env tidak disetting, pakai nilai default acak (sebagai fallback keamanan)
        $superAdminEmail = env('SUPERADMIN_EMAIL', 'superadmin@inventaris.com');
        $superAdminPass  = env('SUPERADMIN_PASSWORD', 'superadmin123!');

        // 1. Buat Akun Superadmin
        User::updateOrCreate(
            ['email' => $superAdminEmail], // Cari berdasarkan email
            [
                'name' => 'Super Administrator',
                'password' => $superAdminPass, 
                'role' => 'superadmin',
            ]
        );

        // 2. Buat Akun Admin
        User::updateOrCreate(
            ['email' => 'admin@inventaris.com'],
            [
                'name' => 'Administrator',
                'password' => 'admin123!',
                'role' => 'admin',
            ]
        );

        // 3. Buat Akun Staff (Kasir)
        User::updateOrCreate(
            ['email' => 'staff@inventaris.com'],
            [
                'name' => 'Staff Kasir',
                'password' => 'staff123!',
                'role' => 'staff',
            ]
        );
    }
}
