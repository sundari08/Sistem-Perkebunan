<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Contract\Database;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportUserFirebase extends Command
{
    protected $signature = 'import:users-firebase';
    protected $description = 'Import data user dari Excel ke Firebase Realtime Database';

    protected $database;

    public function __construct(Database $database)
    {
        parent::__construct();
        $this->database = $database;
    }

    public function handle()
    {
        $filePath = storage_path('app/OTORISASI.xlsx');
        
        if (!file_exists($filePath)) {
            $this->error("File tidak ditemukan: " . $filePath);
            return 1;
        }
        
        $this->info("📂 Membaca file: " . basename($filePath));
        
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getSheet(0);
        $rows = $sheet->toArray();
        
        // Hapus header
        array_shift($rows);
        
        // Hapus data user lama di Firebase
        $this->database->getReference('users')->remove();
        $this->info("🗑️ Data user lama dihapus");
        
        // Tambah user ADMIN
        $adminData = [
            'username' => 'admin',
            'password' => password_hash('PGS@123?!', PASSWORD_DEFAULT),
            'unit' => 'ADMIN',
            'jabatan' => 'ADMIN',
            'estate' => null,
            'divisi' => null,
            'otorisasi' => 'admin_full_access',
            'created_at' => now()->toISOString(),
        ];
        $this->database->getReference('users')->push($adminData);
        $this->info("👑 User Admin ditambahkan");
        
        $count = 1; // Sudah ada admin
        
        foreach ($rows as $row) {
            $unit = trim($row[0] ?? '');
            $jabatan = trim($row[1] ?? '');
            $username = trim($row[2] ?? '');
            $password = trim($row[3] ?? '');
            $otorisasi = trim($row[4] ?? '');
            
            if (empty($username)) continue;
            
            // Parse estate dan divisi
            $estate = null;
            $divisi = null;
            
            if ($jabatan == 'ASISTEN') {
                $estate = preg_replace('/[0-9]+$/', '', $username);
                $divisi = $password;
            } elseif ($jabatan == 'KERANI') {
                    // Mapping khusus untuk KERANI tertentu
                    $estateMapping = [
                        'MBJE' => 'MBJA',
                        'MRKE' => 'MRKA',
                        'MPTE' => 'MPTA',
                    ];
                    $estate = $estateMapping[$username] ?? $username;
                } elseif (in_array($jabatan, ['ASKEP', 'MANAGER'])) {
                    $estate = str_replace(['ASKEP', 'MANAGER'], '', $username);
            } elseif ($jabatan == 'GENERAL MANAGER') {
                $estate = $unit;
            } elseif ($jabatan == 'DIREKTUR') {
                $estate = 'ALL';
            }
            
            $userData = [
                'username' => $username,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'unit' => $unit,
                'jabatan' => $jabatan,
                'estate' => $estate,
                'divisi' => $divisi,
                'otorisasi' => $otorisasi,
                'created_at' => now()->toISOString(),
            ];
            
            $this->database->getReference('users')->push($userData);
            $count++;
            $this->info("✅ User: {$username} - {$jabatan}");
        }
        
        $this->info("");
        $this->info("✅ IMPORT SELESAI! Total user: {$count}");
        $this->info("👑 Login admin: username=admin, password=PGS@123?!");
        
        return 0;
    }
}