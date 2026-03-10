<?php
// database/seeders/NotificationSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        // Cek apakah tabel notifications ada
        if (!Schema::hasTable('notifications')) {
            $this->command->error('Tabel notifications belum ada. Jalankan migration terlebih dahulu.');
            return;
        }

        // Ambil semua user
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('Tidak ada user ditemukan. Lewati seeder notification.');
            return;
        }

        $this->command->info('Mulai menambahkan notifikasi untuk ' . $users->count() . ' user...');

        $notifications = [];
        $now = now();

        foreach ($users as $user) {
            // Tentukan link berdasarkan role
            $dashboardLink = $this->getDashboardLink($user->role_id);
            
            // 1. Notifikasi Selamat Datang (untuk semua user)
            $notifications[] = [
                'user_id' => $user->id,
                'title' => 'Selamat Datang di SIPPU',
                'message' => 'Selamat datang di Sistem Informasi Perpustakaan UNISBA. Silakan gunakan sistem dengan bijak.',
                'type' => 'success',
                'link' => $dashboardLink,
                'is_read' => false,
                'read_at' => null,
                'created_at' => $now,
                'updated_at' => $now
            ];

            // 2. Notifikasi Informasi Sistem
            $notifications[] = [
                'user_id' => $user->id,
                'title' => 'Pembaruan Sistem',
                'message' => 'Sistem perpustakaan telah diperbarui dengan fitur-fitur terbaru.',
                'type' => 'info',
                'link' => null,
                'is_read' => false,
                'read_at' => null,
                'created_at' => $now,
                'updated_at' => $now
            ];

            // 3. Notifikasi berdasarkan role
            switch ($user->role_id) {
                case 1: // Admin
                    $notifications[] = [
                        'user_id' => $user->id,
                        'title' => 'Laporan Bulanan',
                        'message' => 'Laporan bulanan periode ' . now()->format('F Y') . ' siap untuk direview.',
                        'type' => 'warning',
                        'link' => '/admin/reports',
                        'is_read' => false,
                        'read_at' => null,
                        'created_at' => $now,
                        'updated_at' => $now
                    ];

                    $notifications[] = [
                        'user_id' => $user->id,
                        'title' => 'Pengadaan Baru',
                        'message' => 'Terdapat ' . rand(1, 5) . ' permintaan pengadaan buku yang perlu diproses.',
                        'type' => 'info',
                        'link' => '/admin/procurements',
                        'is_read' => false,
                        'read_at' => null,
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                    break;

                case 2: // Kaprodi
                    $notifications[] = [
                        'user_id' => $user->id,
                        'title' => 'Peminjaman Disetujui',
                        'message' => 'Pengajuan peminjaman buku Anda telah disetujui oleh admin.',
                        'type' => 'success',
                        'link' => '/kaprodi/borrowings',
                        'is_read' => false,
                        'read_at' => null,
                        'created_at' => $now,
                        'updated_at' => $now
                    ];

                    $notifications[] = [
                        'user_id' => $user->id,
                        'title' => 'Buku Jatuh Tempo',
                        'message' => 'Ada ' . rand(1, 3) . ' buku yang akan jatuh tempo dalam 3 hari.',
                        'type' => 'warning',
                        'link' => '/kaprodi/borrowings',
                        'is_read' => false,
                        'read_at' => null,
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                    break;

                case 3: // Supplier
                    $notifications[] = [
                        'user_id' => $user->id,
                        'title' => 'Pesanan Baru',
                        'message' => 'Ada ' . rand(1, 3) . ' pesanan baru yang perlu dikonfirmasi.',
                        'type' => 'info',
                        'link' => '/supplier/procurements',
                        'is_read' => false,
                        'read_at' => null,
                        'created_at' => $now,
                        'updated_at' => $now
                    ];

                    $notifications[] = [
                        'user_id' => $user->id,
                        'title' => 'Pengiriman',
                        'message' => 'Jangan lupa untuk mengupdate status pengiriman pesanan.',
                        'type' => 'warning',
                        'link' => '/supplier/procurements',
                        'is_read' => false,
                        'read_at' => null,
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                    break;
            }
        }

        // Insert notifikasi ke database
        $totalInserted = 0;
        foreach (array_chunk($notifications, 100) as $chunk) {
            DB::table('notifications')->insert($chunk);
            $totalInserted += count($chunk);
        }

        $this->command->info("Berhasil menambahkan {$totalInserted} notifikasi.");
    }

    /**
     * Get dashboard link based on user role
     */
    private function getDashboardLink($roleId)
    {
        switch ($roleId) {
            case 1:
                return '/admin/dashboard';
            case 2:
                return '/kaprodi/dashboard';
            case 3:
                return '/supplier/dashboard';
            default:
                return '/dashboard';
        }
    }
}