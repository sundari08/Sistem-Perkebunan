<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Hapus data lama
        DB::table('users')->truncate();
        
        // ========== TAMBAHKAN USER ADMIN ==========
        DB::table('users')->insert([
            'username' => 'admin',
            'password' => Hash::make('PGS@123?!'),
            'unit' => 'ADMIN',
            'jabatan' => 'ADMIN',
            'estate' => null,
            'divisi' => null,
            'otorisasi' => 'admin_full_access',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Path ke file Excel
        $filePath = storage_path('app/OTORISASI.xlsx');
        
        if (!file_exists($filePath)) {
            $this->command->error("File tidak ditemukan: " . $filePath);
            $this->command->info("User Admin sudah ditambahkan!");
            return;
        }
        
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();
        
        // Hapus header (baris pertama)
        array_shift($rows);
        
        foreach ($rows as $row) {
            $unit = trim($row[0] ?? '');
            $jabatan = trim($row[1] ?? '');
            $username = trim($row[2] ?? '');
            $password = trim($row[3] ?? '');
            $otorisasi = trim($row[4] ?? '');
            
            // Skip baris kosong
            if (empty($username)) continue;
            
            // Parse estate dan divisi dari username
            $estate = null;
            $divisi = null;
            
            if ($jabatan == 'ASISTEN') {
                // Ambil estate dari username (hapus angka di akhir)
                $estate = preg_replace('/[0-9]+$/', '', $username);
                // Divisi dari password
                $divisi = $password;
            } elseif (in_array($jabatan, ['KERANI', 'ASKEP', 'MANAGER'])) {
                $estate = $username;
            } elseif ($jabatan == 'GENERAL MANAGER') {
                $estate = $unit; // Untuk GM, estate diisi dengan unitnya (PG1A, PG1B, PG2)
            } elseif ($jabatan == 'DIREKTUR') {
                $estate = 'ALL';
            }
            
            DB::table('users')->insert([
                'username' => $username,
                'password' => Hash::make($password),
                'unit' => $unit,
                'jabatan' => $jabatan,
                'estate' => $estate,
                'divisi' => $divisi,
                'otorisasi' => $otorisasi,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        $this->command->info("✅ User berhasil diimport!");
        $this->command->info("👑 Admin: username=admin, password=PGS@123?!");
    }
}