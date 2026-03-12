<?php
// database/seeders/UserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Supplier;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get role IDs
        $adminRole = Role::where('name', 'admin')->first();
        $kaprodiRole = Role::where('name', 'kaprodi')->first();
        $supplierRole = Role::where('name', 'supplier')->first();

        if (!$adminRole || !$kaprodiRole || !$supplierRole) {
            $this->command->error('Roles not found. Please run RoleSeeder first.');
            return;
        }

        // ==================== 1. CREATE ADMIN ====================
        User::updateOrCreate(
            ['email' => 'admin@library.unisba.ac.id'],
            [
                'name' => 'Admin Perpustakaan',
                'email' => 'admin@library.unisba.ac.id',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'phone_number' => '081234567890',
                'nip' => '197001012005011001',
                'department' => 'Perpustakaan',
                'faculty' => 'Universitas',
                'address' => 'Jl. Tamansari No. 20, Bandung',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // ==================== 2. CREATE KAPRODI ====================
        $this->createKaprodi($kaprodiRole->id);

        // ==================== 3. CREATE SUPPLIER USERS ====================
        $this->createSupplierUsers($supplierRole->id);

        $this->command->info('Users seeded successfully!');
    }

    /**
     * Create Kaprodi users
     */
    private function createKaprodi($kaprodiRoleId)
    {
        // Data fakultas dan prodi
        $fakultas = [
            'Fakultas Kedokteran' => [
                'prodi' => ['Pendidikan Dokter', 'Profesi Dokter', 'Farmasi'],
                'kode' => 'FK'
            ],
            'Fakultas Ilmu Komunikasi' => [
                'prodi' => ['Ilmu Komunikasi (Jurnalistik)', 'Ilmu Komunikasi (Hubungan Masyarakat)', 'Ilmu Komunikasi (Manajemen Produksi Media)'],
                'kode' => 'FIKOM'
            ],
            'Fakultas Hukum' => [
                'prodi' => ['Ilmu Hukum'],
                'kode' => 'FH'
            ],
            'Fakultas Psikologi' => [
                'prodi' => ['Psikologi'],
                'kode' => 'FPSI'
            ],
            'Fakultas Ekonomi & Bisnis' => [
                'prodi' => ['Manajemen', 'Akuntansi', 'Ekonomi Pembangunan'],
                'kode' => 'FEB'
            ],
            'Fakultas Teknik' => [
                'prodi' => ['Teknik Pertambangan', 'Teknik Industri', 'Teknik Sipil', 'Perencanaan Wilayah dan Kota'],
                'kode' => 'FT'
            ],
            'Fakultas MIPA' => [
                'prodi' => ['Matematika', 'Statistika'],
                'kode' => 'FMIPA'
            ],
            'Fakultas Syariah' => [
                'prodi' => ['Hukum Ekonomi Syariah', 'Hukum Keluarga Islam', 'Hukum Pidana Islam'],
                'kode' => 'FSH'
            ],
            'Fakultas Tarbiyah & Keguruan' => [
                'prodi' => ['Pendidikan Agama Islam', 'Pendidikan Guru PAUD'],
                'kode' => 'FTK'
            ],
            'Fakultas Dakwah' => [
                'prodi' => ['Komunikasi dan Penyiaran Islam'],
                'kode' => 'FD'
            ]
        ];

        $kaprodiList = [];
        $nipCounter = 1;

        foreach ($fakultas as $namaFakultas => $data) {
            foreach ($data['prodi'] as $namaProdi) {
                // Generate NIP dengan format D + 6 digit angka
                $nip = 'D' . str_pad($nipCounter, 6, '0', STR_PAD_LEFT);
                
                // USERNAME MENGGUNAKAN NIP (format sama dengan NIP)
                $username = $nip;
                
                // PASSWORD MENGGUNAKAN NIP (akan di-hash)
                $password = $nip;
                
                // Generate email (tetap menggunakan format username@unisba.ac.id)
                $email = strtolower(str_replace('D', 'd', $username)) . '@unisba.ac.id';
                
                // Nama Kaprodi
                $namaKaprodi = $this->generateKaprodiName($namaProdi);
                
                $kaprodiList[] = [
                    'name' => $namaKaprodi,
                    'email' => $email,
                    'username' => $username,        // Username = NIP
                    'password' => Hash::make($password), // Password = NIP (di-hash)
                    'role_id' => $kaprodiRoleId,
                    'phone_number' => $this->generatePhoneNumber(),
                    'nip' => $nip,
                    'department' => $namaProdi,
                    'faculty' => $namaFakultas,
                    'address' => $this->generateAddress(),
                    'is_active' => true,
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                
                $nipCounter++;
            }
        }

        // Insert all kaprodi
        $createdCount = 0;
        foreach ($kaprodiList as $kaprodi) {
            $user = User::updateOrCreate(
                ['email' => $kaprodi['email']],
                $kaprodi
            );
            $createdCount++;
        }

        $this->command->info('Created ' . $createdCount . ' kaprodi users');
        $this->command->info('Username and password for kaprodi = NIP (contoh: D000001)');
    }

    /**
     * Create Supplier users
     */
    private function createSupplierUsers($supplierRoleId)
    {
        $suppliers = Supplier::where('is_active', true)->get();
        
        if ($suppliers->isEmpty()) {
            $this->command->warn('No suppliers found. Skipping supplier users creation.');
            return;
        }

        $supplierUsers = [];
        foreach ($suppliers as $index => $supplier) {
            // Generate username dari nama supplier
            $username = Str::slug($supplier->name, '_');
            
            // Generate NIP khusus supplier (opsional)
            $nip = 'S' . str_pad($index + 1, 6, '0', STR_PAD_LEFT);
            
            $supplierUsers[] = [
                'name' => $supplier->name,
                'email' => $supplier->email,
                'username' => $username,
                'password' => Hash::make('password'), // Password default 'password' untuk supplier
                'role_id' => $supplierRoleId,
                'supplier_id' => $supplier->id,
                'phone_number' => $supplier->phone_number,
                'nip' => $nip, // Tambahkan NIP untuk supplier
                'address' => $supplier->address,
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        $createdCount = 0;
        foreach ($supplierUsers as $supplierUser) {
            User::updateOrCreate(
                ['email' => $supplierUser['email']],
                $supplierUser
            );
            $createdCount++;
        }

        $this->command->info('Created ' . $createdCount . ' supplier users');
    }

    /**
     * Generate random kaprodi name (sama seperti sebelumnya)
     */
    private function generateKaprodiName($prodi)
    {
        $firstNames = ['Ahmad', 'Muhammad', 'Abdul', 'Siti', 'Nur', 'Rina', 'Dedi', 'Euis', 'Yayan', 'Tati', 'Asep', 'Ika', 'Rudi', 'Dewi', 'Agus', 'Ida', 'Usep', 'Yanti', 'Cecep', 'Nina'];
        $lastNames = ['Rahman', 'Hidayat', 'Abdullah', 'Nugraha', 'Kusuma', 'Pratama', 'Wijaya', 'Hasanah', 'Fitriani', 'Maulana', 'Ramdhan', 'Fauziah', 'Solihin', 'Rachman', 'Kurniawan', 'Puspita', 'Hermawan', 'Lestari', 'Gunawan', 'Susanti'];
        
        $firstName = $firstNames[array_rand($firstNames)];
        $lastName = $lastNames[array_rand($lastNames)];
        
        // Tambahkan gelar akademis berdasarkan prodi
        if (strpos($prodi, 'Dokter') !== false) {
            $title = 'dr.';
            $degree = ', Sp.';
            return $title . ' ' . $firstName . ' ' . $lastName . $degree;
        } elseif (strpos($prodi, 'Hukum') !== false) {
            $title = 'Dr.';
            $degree = ', S.H., M.H.';
        } elseif (strpos($prodi, 'Agama') !== false || strpos($prodi, 'Syariah') !== false || strpos($prodi, 'Islam') !== false) {
            $title = 'Dr. H.';
            $degree = ', M.Ag';
            return $title . ' ' . $firstName . ' ' . $lastName . $degree;
        } elseif (strpos($prodi, 'Ekonomi') !== false || strpos($prodi, 'Manajemen') !== false || strpos($prodi, 'Akuntansi') !== false) {
            $title = 'Dr.';
            $degree = ', S.E., M.M.';
        } elseif (strpos($prodi, 'Teknik') !== false) {
            $title = 'Dr.';
            $degree = ', S.T., M.T.';
        } elseif (strpos($prodi, 'MIPA') !== false || strpos($prodi, 'Matematika') !== false || strpos($prodi, 'Statistika') !== false) {
            $title = 'Dr.';
            $degree = ', M.Si.';
        } elseif (strpos($prodi, 'Komunikasi') !== false) {
            $title = 'Dr.';
            $degree = ', S.Sos., M.Si.';
        } elseif (strpos($prodi, 'Psikologi') !== false) {
            $title = 'Dr.';
            $degree = ', S.Psi., M.Psi.';
        } else {
            $title = 'Dr.';
            $degree = ', M.Pd.';
        }
        
        return $title . ' ' . $firstName . ' ' . $lastName . $degree;
    }

    /**
     * Generate random phone number
     */
    private function generatePhoneNumber()
    {
        $prefix = ['0812', '0813', '0821', '0822', '0852', '0853', '0878'];
        return $prefix[array_rand($prefix)] . rand(10000000, 99999999);
    }

    /**
     * Generate random address
     */
    private function generateAddress()
    {
        $streets = ['Jl. Tamansari', 'Jl. Dipatiukur', 'Jl. Cihampelas', 'Jl. Setiabudi', 'Jl. Suryalaya', 'Jl. Terusan', 'Jl. Pasirkaliki', 'Jl. Sukajadi', 'Jl. Pelesiran', 'Jl. Ciumbuleuit'];
        $numbers = rand(1, 200);
        $cities = ['Bandung', 'Bandung', 'Bandung', 'Cimahi', 'Bandung'];
        
        $street = $streets[array_rand($streets)];
        $city = $cities[array_rand($cities)];
        
        return $street . ' No. ' . $numbers . ', ' . $city;
    }
}