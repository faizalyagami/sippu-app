<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Admin Perpustakaan',
            'email' => 'admin@library.unisba.ac.id',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role_id' => 1, // Admin role
            'nip' => '197001012005011001',
            'department' => 'Perpustakaan',
            'faculty' => 'Universitas',
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}