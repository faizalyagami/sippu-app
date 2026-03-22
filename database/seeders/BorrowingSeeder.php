<?php
// database/seeders/BorrowingSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Borrowing;
use App\Models\BorrowingItem;
use App\Models\User;
use App\Models\Book;
use Carbon\Carbon;

class BorrowingSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting to seed permintaan data...');

        // Ambil semua user kaprodi (role_id = 2)
        $kaprodiUsers = User::where('role_id', 2)->get();

        // Ambil semua buku yang aktif
        $books = Book::where('is_active', true)->get();

        if ($kaprodiUsers->isEmpty()) {
            $this->command->error('No kaprodi users found. Please run UserSeeder first.');
            return;
        }

        if ($books->isEmpty()) {
            $this->command->error('No books found. Please run BookSeeder first.');
            return;
        }

        $totalBorrowings = 0;
        $now = Carbon::now();

        // Buat 50 data permintaan
        for ($i = 1; $i <= 50; $i++) {
            // Pilih user kaprodi secara acak
            $user = $kaprodiUsers->random();

            // Pilih 1-3 buku secara acak
            $selectedBooks = $books->random(rand(1, 3));

            // Hitung total items
            $totalItems = 0;
            $borrowingItems = [];

            foreach ($selectedBooks as $book) {
                $quantity = rand(1, min(3, $book->available_stock));
                if ($quantity == 0) continue;

                $totalItems += $quantity;
                $borrowingItems[] = [
                    'book' => $book,
                    'quantity' => $quantity
                ];
            }

            if (empty($borrowingItems)) continue;

            // Generate tanggal permintaan acak (1-60 hari yang lalu)
            $requestDate = Carbon::now()->subDays(rand(1, 60));

            // Tentukan status (hanya 3 status)
            $status = $this->determineStatus();

            // Set expected_return_date ke tanggal yang sama + 14 hari (default)
            $expectedReturnDate = $requestDate->copy()->addDays(14);

            // Buat borrowing (sebagai permintaan)
            $borrowing = Borrowing::create([
                'user_id' => $user->id,
                'approved_by' => ($status != 'pending') ? 1 : null, // Admin id = 1
                'borrowing_date' => $requestDate,
                'expected_return_date' => $expectedReturnDate, // Beri nilai default
                'actual_return_date' => null,
                'status' => $status,
                'purpose' => $this->getRandomPurpose(),
                'notes' => rand(0, 1) ? 'Catatan: ' . $this->getRandomNotes() : null,
                'rejection_reason' => $status == 'cancelled' ? $this->getRandomRejectionReason() : null,
                'total_items' => $totalItems,
                'created_at' => $requestDate,
                'updated_at' => ($status != 'pending') ? $requestDate->copy()->addHours(rand(1, 48)) : $requestDate,
            ]);

            // Buat borrowing items
            foreach ($borrowingItems as $item) {
                $book = $item['book'];
                $quantity = $item['quantity'];

                BorrowingItem::create([
                    'borrowing_id' => $borrowing->id,
                    'book_id' => $book->id,
                    'quantity' => $quantity,
                    'returned_quantity' => 0,
                    'damaged_quantity' => 0,
                    'lost_quantity' => 0,
                    'status' => 'pending',
                    'condition_notes' => null,
                    'return_date' => null,
                    'created_at' => $requestDate,
                    'updated_at' => $requestDate,
                ]);
            }

            $totalBorrowings++;

            if ($i % 10 == 0) {
                $this->command->info("Created {$i} permintaan...");
            }
        }

        $this->command->info("Successfully seeded {$totalBorrowings} permintaan!");
    }

    /**
     * Determine status (hanya 3 status)
     */
    private function determineStatus()
    {
        $rand = rand(1, 100);

        // Distribusi: 40% pending, 35% approved, 25% cancelled
        if ($rand <= 40) {
            return 'pending';
        } elseif ($rand <= 75) {
            return 'approved';
        } else {
            return 'cancelled';
        }
    }

    /**
     * Get random purpose
     */
    private function getRandomPurpose()
    {
        $purposes = [
            'Untuk bahan ajar mata kuliah',
            'Referensi penelitian tugas akhir',
            'Bahan skripsi mahasiswa',
            'Persiapan presentasi seminar',
            'Studi literatur untuk jurnal',
            'Pengembangan materi kuliah',
            'Penelitian dosen',
            'Tugas kelompok mahasiswa',
            'Bahan diskusi kelas',
            'Referensi praktikum',
        ];

        return $purposes[array_rand($purposes)];
    }

    /**
     * Get random notes
     */
    private function getRandomNotes()
    {
        $notes = [
            'Mohon segera diproses',
            'Butuh untuk minggu depan',
            'Buku sangat diperlukan',
            'Terima kasih atas bantuannya',
            'Semoga tersedia',
            'Untuk keperluan mendesak',
        ];

        return $notes[array_rand($notes)];
    }

    /**
     * Get random rejection reason
     */
    private function getRandomRejectionReason()
    {
        $reasons = [
            'Stok buku tidak mencukupi',
            'Buku sedang dalam proses perbaikan',
            'Buku hanya untuk referensi di tempat',
            'Melebihi batas maksimal permintaan',
            'Data permintaan tidak lengkap',
            'Buku tidak tersedia untuk dipinjam',
            'Sedang dalam masa pemeliharaan',
            'Buku sudah dipesan oleh peminjam lain',
        ];

        return $reasons[array_rand($reasons)];
    }
}
