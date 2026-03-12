<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Kedokteran
            ['name' => 'Pendidikan Dokter', 'code' => 'PD', 'parent' => null],
            ['name' => 'Profesi Dokter', 'code' => 'PRODOK', 'parent' => null],
            ['name' => 'Farmasi', 'code' => 'FAR', 'parent' => null],
            
            // Ilmu Komunikasi
            ['name' => 'Ilmu Komunikasi (Jurnalistik)', 'code' => 'JUR', 'parent' => null],
            ['name' => 'Ilmu Komunikasi (Hubungan Masyarakat)', 'code' => 'HUMAS', 'parent' => null],
            ['name' => 'Ilmu Komunikasi (Manajemen Produksi Media)', 'code' => 'MPM', 'parent' => null],
            
            // Hukum
            ['name' => 'Ilmu Hukum', 'code' => 'HUK', 'parent' => null],
            
            // Psikologi
            ['name' => 'Psikologi', 'code' => 'PSI', 'parent' => null],
            
            // Ekonomi & Bisnis
            ['name' => 'Manajemen', 'code' => 'MAN', 'parent' => null],
            ['name' => 'Akuntansi', 'code' => 'AKU', 'parent' => null],
            ['name' => 'Ekonomi Pembangunan', 'code' => 'EP', 'parent' => null],
            
            // Teknik
            ['name' => 'Teknik Pertambangan', 'code' => 'TAMB', 'parent' => null],
            ['name' => 'Teknik Industri', 'code' => 'TI', 'parent' => null],
            ['name' => 'Teknik Sipil', 'code' => 'TS', 'parent' => null],
            ['name' => 'Perencanaan Wilayah dan Kota', 'code' => 'PWK', 'parent' => null],
            
            // MIPA
            ['name' => 'Matematika', 'code' => 'MATE', 'parent' => null],
            ['name' => 'Statistika', 'code' => 'STAT', 'parent' => null],
            
            // Syariah
            ['name' => 'Hukum Ekonomi Syariah', 'code' => 'HES', 'parent' => null],
            ['name' => 'Hukum Keluarga Islam', 'code' => 'HKI', 'parent' => null],
            ['name' => 'Hukum Pidana Islam', 'code' => 'HPI', 'parent' => null],
            
            // Tarbiyah & Keguruan
            ['name' => 'Pendidikan Agama Islam', 'code' => 'PAI', 'parent' => null],
            ['name' => 'Pendidikan Guru PAUD', 'code' => 'PGPAUD', 'parent' => null],
            
            // Dakwah
            ['name' => 'Komunikasi dan Penyiaran Islam', 'code' => 'KPI', 'parent' => null],
        ];

        foreach ($categories as $cat) {
            Category::create([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'code' => $cat['code'],
                'description' => "Kategori buku untuk {$cat['name']}",
                'is_active' => true,
            ]);
        }

        $this->command->info('Categories seeded successfully!');
    }
}