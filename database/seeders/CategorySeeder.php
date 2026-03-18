<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Buat parent categories (Fakultas) terlebih dahulu
        $faculties = [
            ['name' => 'Fakultas Kedokteran', 'code' => 'FK'],
            ['name' => 'Fakultas Ilmu Komunikasi', 'code' => 'FIKOM'],
            ['name' => 'Fakultas Hukum', 'code' => 'FH'],
            ['name' => 'Fakultas Psikologi', 'code' => 'FPSI'],
            ['name' => 'Fakultas Ekonomi & Bisnis', 'code' => 'FEB'],
            ['name' => 'Fakultas Teknik', 'code' => 'FT'],
            ['name' => 'Fakultas MIPA', 'code' => 'FMIPA'],
            ['name' => 'Fakultas Syariah', 'code' => 'FSH'],
            ['name' => 'Fakultas Tarbiyah & Keguruan', 'code' => 'FTK'],
            ['name' => 'Fakultas Dakwah', 'code' => 'FD'],
        ];

        $facultyIds = [];

        foreach ($faculties as $faculty) {
            $category = Category::create([
                'name' => $faculty['name'],
                'slug' => Str::slug($faculty['name']),
                'code' => $faculty['code'],
                'description' => "Kategori untuk {$faculty['name']}",
                'is_active' => true,
                'parent_id' => null, // Fakultas tidak memiliki parent
            ]);
            
            $facultyIds[$faculty['name']] = $category->id;
            $this->command->info("Created parent category: {$faculty['name']}");
        }

        // Buat child categories (Program Studi) dengan parent_id yang sesuai
        $programStudi = [
            // Fakultas Kedokteran
            [
                'name' => 'Pendidikan Dokter', 
                'code' => 'PD', 
                'faculty' => 'Fakultas Kedokteran'
            ],
            [
                'name' => 'Profesi Dokter', 
                'code' => 'PRODOK', 
                'faculty' => 'Fakultas Kedokteran'
            ],
            [
                'name' => 'Farmasi', 
                'code' => 'FAR', 
                'faculty' => 'Fakultas Kedokteran'
            ],
            
            // Fakultas Ilmu Komunikasi
            [
                'name' => 'Ilmu Komunikasi (Jurnalistik)', 
                'code' => 'JUR', 
                'faculty' => 'Fakultas Ilmu Komunikasi'
            ],
            [
                'name' => 'Ilmu Komunikasi (Hubungan Masyarakat)', 
                'code' => 'HUMAS', 
                'faculty' => 'Fakultas Ilmu Komunikasi'
            ],
            [
                'name' => 'Ilmu Komunikasi (Manajemen Produksi Media)', 
                'code' => 'MPM', 
                'faculty' => 'Fakultas Ilmu Komunikasi'
            ],
            
            // Fakultas Hukum
            [
                'name' => 'Ilmu Hukum', 
                'code' => 'HUK', 
                'faculty' => 'Fakultas Hukum'
            ],
            
            // Fakultas Psikologi
            [
                'name' => 'Psikologi', 
                'code' => 'PSI', 
                'faculty' => 'Fakultas Psikologi'
            ],
            
            // Fakultas Ekonomi & Bisnis
            [
                'name' => 'Manajemen', 
                'code' => 'MAN', 
                'faculty' => 'Fakultas Ekonomi & Bisnis'
            ],
            [
                'name' => 'Akuntansi', 
                'code' => 'AKU', 
                'faculty' => 'Fakultas Ekonomi & Bisnis'
            ],
            [
                'name' => 'Ekonomi Pembangunan', 
                'code' => 'EP', 
                'faculty' => 'Fakultas Ekonomi & Bisnis'
            ],
            
            // Fakultas Teknik
            [
                'name' => 'Teknik Pertambangan', 
                'code' => 'TAMB', 
                'faculty' => 'Fakultas Teknik'
            ],
            [
                'name' => 'Teknik Industri', 
                'code' => 'TI', 
                'faculty' => 'Fakultas Teknik'
            ],
            [
                'name' => 'Teknik Sipil', 
                'code' => 'TS', 
                'faculty' => 'Fakultas Teknik'
            ],
            [
                'name' => 'Perencanaan Wilayah dan Kota', 
                'code' => 'PWK', 
                'faculty' => 'Fakultas Teknik'
            ],
            
            // Fakultas MIPA
            [
                'name' => 'Matematika', 
                'code' => 'MATE', 
                'faculty' => 'Fakultas MIPA'
            ],
            [
                'name' => 'Statistika', 
                'code' => 'STAT', 
                'faculty' => 'Fakultas MIPA'
            ],
            
            // Fakultas Syariah
            [
                'name' => 'Hukum Ekonomi Syariah', 
                'code' => 'HES', 
                'faculty' => 'Fakultas Syariah'
            ],
            [
                'name' => 'Hukum Keluarga Islam', 
                'code' => 'HKI', 
                'faculty' => 'Fakultas Syariah'
            ],
            [
                'name' => 'Hukum Pidana Islam', 
                'code' => 'HPI', 
                'faculty' => 'Fakultas Syariah'
            ],
            
            // Fakultas Tarbiyah & Keguruan
            [
                'name' => 'Pendidikan Agama Islam', 
                'code' => 'PAI', 
                'faculty' => 'Fakultas Tarbiyah & Keguruan'
            ],
            [
                'name' => 'Pendidikan Guru PAUD', 
                'code' => 'PGPAUD', 
                'faculty' => 'Fakultas Tarbiyah & Keguruan'
            ],
            
            // Fakultas Dakwah
            [
                'name' => 'Komunikasi dan Penyiaran Islam', 
                'code' => 'KPI', 
                'faculty' => 'Fakultas Dakwah'
            ],
        ];

        foreach ($programStudi as $prodi) {
            $parentId = $facultyIds[$prodi['faculty']] ?? null;
            
            Category::create([
                'name' => $prodi['name'],
                'slug' => Str::slug($prodi['name']),
                'code' => $prodi['code'],
                'description' => "Kategori buku untuk {$prodi['name']} - {$prodi['faculty']}",
                'is_active' => true,
                'parent_id' => $parentId,
            ]);
            
            $this->command->info("Created child category: {$prodi['name']} under {$prodi['faculty']}");
        }

        $this->command->info('Categories seeded successfully with parent-child relationships!');
        $this->command->info('Total: ' . (count($faculties) + count($programStudi)) . ' categories created.');
    }
}