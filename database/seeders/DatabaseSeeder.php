<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            CategorySeeder::class,
            SupplierSeeder::class,   // Buat supplier dulu
            // AdminUserSeeder::class,  // Admin user
            UserSeeder::class,
            BookSeeder::class,
            BorrowingSeeder::class,
            NotificationSeeder::class
        ]);
    }
}
