<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    public function run()
    {
        $suppliers = [
            [
                'name' => 'PT. Gramedia Pustaka Utama',
                'company_name' => 'PT. Gramedia Pustaka Utama',
                'email' => 'gramedia@gramedia.com',
                'phone_number' => '021-12345678',
                'address' => 'Jl. Palmerah Selatan No. 1, Jakarta',
                'npwp' => '01.234.567.8-901.000',
                'contact_person' => 'Budi Santoso',
                'cp_phone' => '081234567890',
                'description' => 'Penerbit dan distributor buku terkemuka di Indonesia',
                'is_active' => true
            ],
            [
                'name' => 'CV. Mizan Media Utama',
                'company_name' => 'CV. Mizan Media Utama',
                'email' => 'mizan@mizan.com',
                'phone_number' => '022-12345678',
                'address' => 'Jl. Cihampelas No. 123, Bandung',
                'npwp' => '02.345.678.9-012.000',
                'contact_person' => 'Ahmad Fauzi',
                'cp_phone' => '081234567891',
                'description' => 'Penerbit buku Islam dan umum',
                'is_active' => true
            ],
            [
                'name' => 'PT. Erlangga',
                'company_name' => 'PT. Penerbit Erlangga',
                'email' => 'erlangga@erlangga.co.id',
                'phone_number' => '021-12345679',
                'address' => 'Jl. H. Baping No. 100, Jakarta Timur',
                'npwp' => '03.456.789.0-123.000',
                'contact_person' => 'Siti Nurhaliza',
                'cp_phone' => '081234567892',
                'description' => 'Penerbit buku pendidikan',
                'is_active' => true
            ],
            [
                'name' => 'PT. RajaGrafindo Persada',
                'company_name' => 'PT. RajaGrafindo Persada',
                'email' => 'rajagrafindo@rajagrafindo.co.id',
                'phone_number' => '021-12345680',
                'address' => 'Jl. Raya Leuwinanggung No. 112, Depok',
                'npwp' => '04.567.890.1-234.000',
                'contact_person' => 'Rudi Hartono',
                'cp_phone' => '081234567893',
                'description' => 'Penerbit buku perguruan tinggi',
                'is_active' => true
            ],
            [
                'name' => 'CV. Andi Offset',
                'company_name' => 'CV. Andi Offset',
                'email' => 'andi@andioffset.com',
                'phone_number' => '0274-1234567',
                'address' => 'Jl. Beo No. 38-40, Yogyakarta',
                'npwp' => '05.678.901.2-345.000',
                'contact_person' => 'Joko Widodo',
                'cp_phone' => '081234567894',
                'description' => 'Penerbit buku teknik dan komputer',
                'is_active' => true
            ]
        ];

        foreach ($suppliers as $supplier) {
            Supplier::updateOrCreate(
                ['email' => $supplier['email']],
                $supplier
            );
        }
    }
}