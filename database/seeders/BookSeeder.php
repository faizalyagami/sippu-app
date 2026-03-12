<?php
// database/seeders/BookSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Category;
use App\Models\BookCondition;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan categories sudah ada
        $categories = Category::all();
        if ($categories->isEmpty()) {
            $this->command->error('Categories not found. Please run CategorySeeder first.');
            return;
        }

        $this->command->info('Starting to seed books...');

        // Data buku berdasarkan kategori (sesuaikan dengan nama kolom yang benar)
        $booksByCategory = [
            'Pendidikan Dokter' => [
                ['title' => 'Buku Ajar Ilmu Penyakit Dalam', 'author' => 'dr. Ahmad Fauzi, Sp.PD', 'publisher' => 'Salemba Medika', 'isbn' => '978-602-1234-01-5', 'year' => 2023],
                ['title' => 'Atlas Anatomi Manusia', 'author' => 'Prof. Dr. Frank H. Netter', 'publisher' => 'EGC', 'isbn' => '978-979-1234-02-2', 'year' => 2022],
                ['title' => 'Farmakologi Dasar', 'author' => 'Dr. Budi Santoso, M.Farm', 'publisher' => 'UI Press', 'isbn' => '978-602-1234-03-9', 'year' => 2023],
                ['title' => 'Imunologi Dasar', 'author' => 'Prof. Dr. Siti Aminah', 'publisher' => 'Gadjah Mada University Press', 'isbn' => '978-602-1234-04-6', 'year' => 2021],
                ['title' => 'Patologi Robbins', 'author' => 'Kumar, Abbas, Aster', 'publisher' => 'Elsevier', 'isbn' => '978-602-1234-05-3', 'year' => 2024],
            ],
            'Profesi Dokter' => [
                ['title' => 'Buku Saku Dokter Gigi', 'author' => 'Drg. Dewi Lestari', 'publisher' => 'Salemba Medika', 'isbn' => '978-602-1234-06-0', 'year' => 2022],
                ['title' => 'Ilmu Kesehatan Anak', 'author' => 'Prof. dr. Siti Nurhaliza, Sp.A', 'publisher' => 'EGC', 'isbn' => '978-602-1234-07-7', 'year' => 2023],
                ['title' => 'Bedah Dasar', 'author' => 'dr. Ahmad Hidayat, Sp.B', 'publisher' => 'Salemba Medika', 'isbn' => '978-602-1234-08-4', 'year' => 2022],
            ],
            'Farmasi' => [
                ['title' => 'Kimia Farmasi', 'author' => 'Dr. apt. Rina Wijaya', 'publisher' => 'Gadjah Mada University Press', 'isbn' => '978-602-1234-09-1', 'year' => 2023],
                ['title' => 'Teknologi Sediaan Farmasi', 'author' => 'apt. Bambang Susanto, M.Farm', 'publisher' => 'UI Press', 'isbn' => '978-602-1234-10-7', 'year' => 2022],
            ],
            'Ilmu Komunikasi (Jurnalistik)' => [
                ['title' => 'Dasar-Dasar Jurnalistik', 'author' => 'Dr. Asep Kurniawan', 'publisher' => 'Rosda Karya', 'isbn' => '978-602-1234-11-4', 'year' => 2023],
                ['title' => 'Teknik Wawancara', 'author' => 'Dewi Sartika, M.Si', 'publisher' => 'Kencana', 'isbn' => '978-602-1234-12-1', 'year' => 2022],
            ],
            'Ilmu Komunikasi (Hubungan Masyarakat)' => [
                ['title' => 'Public Relations: Teori dan Praktik', 'author' => 'Dr. Rina Maulida', 'publisher' => 'Prenadamedia', 'isbn' => '978-602-1234-13-8', 'year' => 2023],
            ],
            'Ilmu Komunikasi (Manajemen Produksi Media)' => [
                ['title' => 'Manajemen Produksi Media', 'author' => 'Ir. Budi Hartono', 'publisher' => 'Andi Offset', 'isbn' => '978-602-1234-14-5', 'year' => 2022],
            ],
            'Ilmu Hukum' => [
                ['title' => 'Pengantar Ilmu Hukum', 'author' => 'Prof. Dr. Jimly Asshiddiqie', 'publisher' => 'Rajawali Press', 'isbn' => '978-602-1234-15-2', 'year' => 2023],
                ['title' => 'Hukum Pidana', 'author' => 'Dr. Andi Hamzah', 'publisher' => 'Sinar Grafika', 'isbn' => '978-602-1234-16-9', 'year' => 2022],
                ['title' => 'Hukum Perdata', 'author' => 'Prof. Subekti', 'publisher' => 'Intermasa', 'isbn' => '978-602-1234-17-6', 'year' => 2021],
                ['title' => 'Hukum Tata Negara', 'author' => 'Dr. Yusril Ihza Mahendra', 'publisher' => 'Pustaka Utama', 'isbn' => '978-602-1234-18-3', 'year' => 2023],
            ],
            'Psikologi' => [
                ['title' => 'Psikologi Umum', 'author' => 'Dr. Sarlito Wirawan', 'publisher' => 'Rajawali Press', 'isbn' => '978-602-1234-19-0', 'year' => 2022],
                ['title' => 'Psikologi Perkembangan', 'author' => 'Prof. Dr. Elizabeth Hurlock', 'publisher' => 'Erlangga', 'isbn' => '978-602-1234-20-6', 'year' => 2023],
                ['title' => 'Psikologi Pendidikan', 'author' => 'Dr. Muhibbin Syah', 'publisher' => 'Rosda Karya', 'isbn' => '978-602-1234-21-3', 'year' => 2022],
            ],
            'Manajemen' => [
                ['title' => 'Manajemen: Teori dan Aplikasi', 'author' => 'Dr. T. Hani Handoko', 'publisher' => 'BPFE', 'isbn' => '978-602-1234-22-0', 'year' => 2023],
                ['title' => 'Sumber Daya Manusia', 'author' => 'Prof. Dr. Malayu Hasibuan', 'publisher' => 'Bumi Aksara', 'isbn' => '978-602-1234-23-7', 'year' => 2022],
            ],
            'Akuntansi' => [
                ['title' => 'Akuntansi Dasar', 'author' => 'Dr. Rudianto, SE, Ak', 'publisher' => 'Erlangga', 'isbn' => '978-602-1234-24-4', 'year' => 2023],
                ['title' => 'Akuntansi Keuangan', 'author' => 'Prof. Dr. Sofyan Safri Harahap', 'publisher' => 'Rajawali Press', 'isbn' => '978-602-1234-25-1', 'year' => 2022],
            ],
            'Ekonomi Pembangunan' => [
                ['title' => 'Ekonomi Pembangunan', 'author' => 'Prof. Dr. Mudrajad Kuncoro', 'publisher' => 'UPP STIM YKPN', 'isbn' => '978-602-1234-26-8', 'year' => 2023],
            ],
            'Teknik Pertambangan' => [
                ['title' => 'Teknik Eksplorasi Tambang', 'author' => 'Ir. Sukandarrumidi', 'publisher' => 'Gadjah Mada University Press', 'isbn' => '978-602-1234-27-5', 'year' => 2022],
            ],
            'Teknik Industri' => [
                ['title' => 'Dasar Teknik Industri', 'author' => 'Dr. Ir. Iftikar Sutalaksana', 'publisher' => 'ITB Press', 'isbn' => '978-602-1234-28-2', 'year' => 2023],
            ],
            'Teknik Sipil' => [
                ['title' => 'Mekanika Tanah', 'author' => 'Prof. Ir. Hary Christady', 'publisher' => 'Gadjah Mada University Press', 'isbn' => '978-602-1234-29-9', 'year' => 2022],
            ],
            'Perencanaan Wilayah dan Kota' => [
                ['title' => 'Perencanaan Kota', 'author' => 'Prof. Dr. Eko Budihardjo', 'publisher' => 'ITB Press', 'isbn' => '978-602-1234-30-5', 'year' => 2021],
            ],
            'Matematika' => [
                ['title' => 'Kalkulus', 'author' => 'Dr. Edwin J. Purcell', 'publisher' => 'Erlangga', 'isbn' => '978-602-1234-31-2', 'year' => 2023],
            ],
            'Statistika' => [
                ['title' => 'Statistika Dasar', 'author' => 'Prof. Dr. Sudjana', 'publisher' => 'Tarsito', 'isbn' => '978-602-1234-32-9', 'year' => 2022],
            ],
            'Hukum Ekonomi Syariah' => [
                ['title' => 'Fiqh Muamalah', 'author' => 'Dr. H. Abdul Rahman, M.Ag', 'publisher' => 'Kencana', 'isbn' => '978-602-1234-33-6', 'year' => 2023],
            ],
            'Hukum Keluarga Islam' => [
                ['title' => 'Hukum Perkawinan Islam', 'author' => 'Prof. Dr. H. Amir Syarifuddin', 'publisher' => 'Prenadamedia', 'isbn' => '978-602-1234-34-3', 'year' => 2022],
            ],
            'Hukum Pidana Islam' => [
                ['title' => 'Jinayat: Hukum Pidana Islam', 'author' => 'Dr. H. Ahmad Wardi', 'publisher' => 'Amzah', 'isbn' => '978-602-1234-35-0', 'year' => 2023],
            ],
            'Pendidikan Agama Islam' => [
                ['title' => 'Ilmu Pendidikan Islam', 'author' => 'Prof. Dr. H. Ramayulis', 'publisher' => 'Kalam Mulia', 'isbn' => '978-602-1234-36-7', 'year' => 2022],
            ],
            'Pendidikan Guru PAUD' => [
                ['title' => 'Psikologi Perkembangan Anak', 'author' => 'Dr. Soemiarti Patmonodewo', 'publisher' => 'Rineka Cipta', 'isbn' => '978-602-1234-37-4', 'year' => 2023],
            ],
            'Komunikasi dan Penyiaran Islam' => [
                ['title' => 'Dasar-Dasar Dakwah', 'author' => 'Dr. H. M. Arifin', 'publisher' => 'Bulan Bintang', 'isbn' => '978-602-1234-38-1', 'year' => 2022],
            ],
        ];

        $totalBooks = 0;

        // Gunakan gambar placeholder lokal
        $coverImages = $this->createLocalPlaceholders();

        foreach ($booksByCategory as $categoryName => $books) {
            // Cari kategori berdasarkan nama
            $category = Category::where('name', $categoryName)->first();
            
            if (!$category) {
                $this->command->warn("Category '{$categoryName}' not found, skipping...");
                continue;
            }

            foreach ($books as $bookData) {
                // Generate random stock
                $totalStock = rand(3, 15);
                $availableStock = rand(1, $totalStock);
                $borrowedStock = $totalStock - $availableStock;
                
                // Get random cover image
                $coverImage = $coverImages[array_rand($coverImages)];
                
                // Perhatikan nama kolom: publication_year atau tahun_terbit?
                // Sesuaikan dengan nama kolom di database Anda
                $book = Book::create([
                    'title' => $bookData['title'],
                    'isbn' => $bookData['isbn'],
                    'author' => $bookData['author'],
                    'publisher' => $bookData['publisher'],
                    'publisher_year' => $bookData['year'],
                    'category_id' => $category->id,
                    'description' => $this->generateDescription($bookData['title'], $bookData['author']),
                    'language' => rand(0, 1) ? 'Indonesia' : 'Inggris',
                    'pages' => rand(150, 500),
                    'cover_image' => $coverImage,
                    'location_rack' => $this->generateLocationRack($category->code ?? 'GEN'),
                    'total_stock' => $totalStock,
                    'available_stock' => $availableStock,
                    'borrowed_stock' => $borrowedStock,
                    'damaged_stock' => rand(0, 2),
                    'lost_stock' => rand(0, 1),
                    'price' => rand(50000, 250000),
                    'is_active' => true,
                ]);

                // Create book conditions for each copy
                for ($i = 1; $i <= $totalStock; $i++) {
                    $condition = 'good';
                    if ($i <= $borrowedStock) {
                        $condition = 'borrowed';
                    } elseif ($i <= $borrowedStock + $book->damaged_stock) {
                        $condition = 'damaged';
                    } elseif ($i <= $borrowedStock + $book->damaged_stock + $book->lost_stock) {
                        $condition = 'lost';
                    }
                    
                    BookCondition::create([
                        'book_id' => $book->id,
                        'condition_code' => 'BC-' . $book->id . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                        'book_code' => $book->id . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                        'condition' => $condition == 'borrowed' ? 'good' : $condition,
                        'last_check_date' => now()->subDays(rand(0, 30)),
                        'checked_by' => 1,
                        'is_available' => $condition == 'good',
                    ]);
                }

                $totalBooks++;
                $this->command->info("Created book: {$bookData['title']}");
            }
        }

        $this->command->info("Successfully seeded {$totalBooks} books!");
    }

    /**
     * Create local placeholder images
     */
    private function createLocalPlaceholders()
    {
        $placeholderPath = public_path('storage/books/covers');
        if (!file_exists($placeholderPath)) {
            mkdir($placeholderPath, 0755, true);
        }

        $colors = [
            'medical' => ['#ff6b6b', '#4ecdc4'],
            'law' => ['#95afc0', '#576574'],
            'business' => ['#f6b93b', '#e55039'],
            'engineering' => ['#78e08f', '#38ada9'],
            'religion' => ['#d980fa', '#9980FA'],
            'science' => ['#12CBC4', '#0652DD'],
            'literature' => ['#FDA7DF', '#ED4C67'],
            'general' => ['#a55eea', '#4b7bec'],
        ];

        $images = [];
        foreach ($colors as $category => $colorPair) {
            for ($i = 1; $i <= 3; $i++) {
                $filename = "{$category}-{$i}.svg";
                $filepath = $placeholderPath . '/' . $filename;
                
                if (!file_exists($filepath)) {
                    $svg = $this->generateSvgPlaceholder($category, $colorPair[0], $colorPair[1]);
                    file_put_contents($filepath, $svg);
                }
                
                $images[] = 'books/covers/' . $filename;
            }
        }

        return $images;
    }

    /**
     * Generate SVG placeholder
     */
    private function generateSvgPlaceholder($category, $color1, $color2)
    {
        $categoryNames = [
            'medical' => 'Kedokteran',
            'law' => 'Hukum',
            'business' => 'Ekonomi',
            'engineering' => 'Teknik',
            'religion' => 'Agama',
            'science' => 'Sains',
            'literature' => 'Sastra',
            'general' => 'Umum',
        ];

        $displayName = $categoryNames[$category] ?? 'Buku';
        
        return <<<SVG
        <svg width="300" height="400" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="grad{$category}" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:{$color1};stop-opacity:1" />
                    <stop offset="100%" style="stop-color:{$color2};stop-opacity:1" />
                </linearGradient>
            </defs>
            <rect width="300" height="400" fill="url(#grad{$category})"/>
            <text x="150" y="200" font-family="Arial" font-size="24" fill="white" text-anchor="middle" dominant-baseline="middle">{$displayName}</text>
            <text x="150" y="250" font-family="Arial" font-size="16" fill="white" text-anchor="middle" dominant-baseline="middle">📚 Buku</text>
        </svg>
        SVG;
    }

    /**
     * Generate random location rack
     */
    private function generateLocationRack($categoryCode)
    {
        $sections = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        $section = $sections[array_rand($sections)];
        $row = rand(1, 10);
        $column = rand(1, 5);
        
        return "{$categoryCode}-{$section}{$row}-{$column}";
    }

    /**
     * Generate description for book
     */
    private function generateDescription($title, $author)
    {
        $descriptions = [
            "Buku ini membahas secara komprehensif tentang {$title}. Cocok untuk mahasiswa dan praktisi.",
            "Karya terbaru dari {$author} yang memberikan pemahaman mendalam tentang {$title}.",
            "Referensi utama untuk mata kuliah terkait dengan pembahasan yang sistematis dan mudah dipahami.",
            "Buku ini dilengkapi dengan studi kasus dan latihan soal untuk memudahkan pemahaman.",
            "Edisi terbaru dengan pembahasan yang diperbaharui sesuai perkembangan terkini.",
            "Buku ajar yang digunakan di berbagai perguruan tinggi terkemuka di Indonesia.",
        ];
        
        return $descriptions[array_rand($descriptions)];
    }
}