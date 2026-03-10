<?php
// database/seeders/RoleSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Insert roles
        DB::table('roles')->insert([
            [
                'name' => 'admin',
                'display_name' => 'Administrator Perpustakaan',
                'description' => 'Admin yang mengelola sistem perpustakaan',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'kaprodi',
                'display_name' => 'Ketua Program Studi',
                'description' => 'Ketua Program Studi yang dapat meminjam buku',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'supplier',
                'display_name' => 'Supplier Buku',
                'description' => 'Pihak ketiga penyedia buku',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Insert admin user
        DB::table('users')->insert([
            'name' => 'Admin Perpustakaan',
            'email' => 'admin@library.unisba.ac.id',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role_id' => 1,
            'phone_number' => '081234567890',
            'nip' => '197001012005011001',
            'department' => 'Perpustakaan',
            'faculty' => 'Universitas',
            'address' => 'Jl. Tamansari No. 20, Bandung',
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Insert sample kaprodi
        DB::table('users')->insert([
            'name' => 'Dr. Ahmad Fauzi, M.Ag',
            'email' => 'kaprodi.fai@unisba.ac.id',
            'username' => 'kaprodi_fai',
            'password' => Hash::make('password'),
            'role_id' => 2,
            'phone_number' => '081234567891',
            'nip' => '197502152005011002',
            'department' => 'Fakultas Agama Islam',
            'faculty' => 'FAI',
            'address' => 'Jl. Tamansari No. 20, Bandung',
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Insert sample supplier
        DB::table('users')->insert([
            'name' => 'PT. Buku Kita',
            'email' => 'supplier@bukukita.com',
            'username' => 'bukukita',
            'password' => Hash::make('password'),
            'role_id' => 3,
            'phone_number' => '081234567892',
            'address' => 'Jl. Soekarno Hatta No. 123, Bandung',
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}