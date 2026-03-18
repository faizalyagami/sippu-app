<?php

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
        $this->command->info('Starting to seed borrowing data...');

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
        $statuses = ['pending', 'approved', 'borrowed', 'returned', 'overdue', 'cancelled'];
        $now = Carbon::now();

        // Buat 50 data peminjaman
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
            
            // Generate tanggal peminjaman acak (1-60 hari yang lalu)
            $borrowingDate = Carbon::now()->subDays(rand(1, 60));
            
            // Status berdasarkan tanggal
            $status = $this->determineStatus($borrowingDate, $now);
            
            // Hitung tanggal pengembalian (14 hari setelah pinjam)
            $expectedReturnDate = $borrowingDate->copy()->addDays(14);
            
            // Tanggal pengembalian aktual (jika status returned)
            $actualReturnDate = null;
            
            if ($status == 'returned') {
                $actualReturnDate = $expectedReturnDate->copy()->addDays(rand(-2, 5));
            }
            
            // Buat borrowing
            $borrowing = Borrowing::create([
                'user_id' => $user->id,
                'approved_by' => $status != 'pending' ? 1 : null,
                'borrowing_date' => $borrowingDate,
                'expected_return_date' => $expectedReturnDate,
                'actual_return_date' => $actualReturnDate,
                'status' => $status,
                'purpose' => $this->getRandomPurpose(),
                'notes' => rand(0, 1) ? 'Catatan: ' . $this->getRandomNotes() : null,
                'rejection_reason' => $status == 'cancelled' ? 'Alasan: ' . $this->getRandomRejectionReason() : null,
                'total_items' => $totalItems,
                'created_at' => $borrowingDate,
                'updated_at' => $borrowingDate->copy()->addHours(rand(1, 48)),
            ]);

            // Buat borrowing items
            foreach ($borrowingItems as $item) {
                $book = $item['book'];
                $quantity = $item['quantity'];
                
                // Update stock jika status bukan pending
                if ($status == 'approved' || $status == 'borrowed') {
                    $book->available_stock -= $quantity;
                    $book->borrowed_stock += $quantity;
                    $book->save();
                } elseif ($status == 'returned') {
                    $book->available_stock += $quantity;
                    $book->borrowed_stock -= $quantity;
                    $book->save();
                }
                
                // Tentukan status item
                $itemStatus = 'borrowed';
                $returnedQty = 0;
                $damagedQty = 0;
                $lostQty = 0;
                
                if ($status == 'returned') {
                    $itemStatus = 'returned';
                    $returnedQty = $quantity;
                    
                    // Kemungkinan rusak atau hilang (5%)
                    if (rand(1, 100) <= 5) {
                        $damagedQty = rand(1, $quantity);
                        $returnedQty = $quantity - $damagedQty;
                        $itemStatus = $damagedQty == $quantity ? 'damaged' : 'partial';
                    } elseif (rand(1, 100) <= 3) {
                        $lostQty = rand(1, $quantity);
                        $returnedQty = $quantity - $lostQty;
                        $itemStatus = $lostQty == $quantity ? 'lost' : 'partial';
                    }
                }
                
                BorrowingItem::create([
                    'borrowing_id' => $borrowing->id,
                    'book_id' => $book->id,
                    'quantity' => $quantity,
                    'returned_quantity' => $returnedQty,
                    'damaged_quantity' => $damagedQty,
                    'lost_quantity' => $lostQty,
                    'status' => $itemStatus,
                    'condition_notes' => ($damagedQty > 0 || $lostQty > 0) ? $this->getRandomConditionNotes() : null,
                    'return_date' => $status == 'returned' ? $actualReturnDate : null,
                    'created_at' => $borrowingDate,
                    'updated_at' => $borrowingDate->copy()->addHours(rand(1, 48)),
                ]);
            }

            $totalBorrowings++;
            
            if ($i % 10 == 0) {
                $this->command->info("Created {$i} borrowings...");
            }
        }

        $this->command->info("Successfully seeded {$totalBorrowings} borrowings!");
    }

    /**
     * Determine status based on dates
     */
    private function determineStatus($borrowingDate, $now)
    {
        $rand = rand(1, 100);
        
        // 15% pending, 20% approved, 25% borrowed, 25% returned, 10% overdue, 5% cancelled
        if ($rand <= 15) {
            return 'pending';
        } elseif ($rand <= 35) {
            return 'approved';
        } elseif ($rand <= 60) {
            return 'borrowed';
        } elseif ($rand <= 85) {
            return 'returned';
        } elseif ($rand <= 95) {
            return 'overdue';
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
            'Bahan ajar mata kuliah',
            'Penelitian tugas akhir',
            'Referensi skripsi',
            'Bahan presentasi',
            'Studi literatur',
            'Persiapan ujian',
            'Pengembangan materi kuliah',
            'Penelitian dosen',
            'Tugas kelompok',
            'Bahan diskusi',
        ];
        
        return $purposes[array_rand($purposes)];
    }

    /**
     * Get random notes
     */
    private function getRandomNotes()
    {
        $notes = [
            'Harap diperpanjang',
            'Buku dalam kondisi baik',
            'Akan dikembalikan tepat waktu',
            'Mohon segera diproses',
            'Butuh untuk minggu depan',
            'Buku sangat membantu',
            'Terima kasih',
        ];
        
        return $notes[array_rand($notes)];
    }

    /**
     * Get random rejection reason
     */
    private function getRandomRejectionReason()
    {
        $reasons = [
            'Stok tidak mencukupi',
            'Buku sedang dalam perbaikan',
            'Data tidak lengkap',
            'Melebihi batas peminjaman',
            'Ada tunggakan peminjaman sebelumnya',
            'Buku hanya untuk referensi di tempat',
        ];
        
        return $reasons[array_rand($reasons)];
    }

    /**
     * Get random condition notes
     */
    private function getRandomConditionNotes()
    {
        $notes = [
            'Beberapa halaman sobek',
            'Cover sedikit rusak',
            'Ada coretan pensil',
            'Halaman terlipat',
            'Binding longgar',
            'Buku terkena air',
            'Halaman hilang',
        ];
        
        return $notes[array_rand($notes)];
    }
}