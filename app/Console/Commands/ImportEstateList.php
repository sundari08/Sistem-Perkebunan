<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Contract\Database;

class ImportMasterDataSederhana extends Command
{
    protected $signature = 'import:master-sederhana';
    protected $description = 'Import master data estate dan divisi (tanpa blok)';

    protected $database;

    public function __construct(Database $database)
    {
        parent::__construct();
        $this->database = $database;
    }

    public function handle()
    {
        // Data master sesuai keinginan
        $data = [
            'MAPE' => ['DE01', 'DE02', 'DE03', 'DE04'],
            'MLGE' => ['DE01', 'DE02', 'DE03', 'DE04', 'DE05'],
            'MPTE' => ['DE01', 'DE02', 'DE03'],
            'MBGA' => ['DP01'],
            'MBGE' => ['DE01', 'DE02', 'DE03'],
            'MKRE' => ['DE01', 'DE02', 'DE03', 'DE04'],
            'MBJA' => ['DP01'],
            'MBJE' => ['DE01', 'DE02', 'DE03'],
            'MRBE' => ['DE01', 'DE02', 'DE03'],
            'MRRA' => ['DP01', 'DP02', 'DP03'],
            'MRKE' => ['DE01', 'DE02', 'DE03'],
            'MRLE' => ['DE01', 'DE02', 'DE03', 'DE04', 'DE05'],
            'MRKA' => ['DP01', 'DP02'],
            'MPTA' => ['DP01'],
        ];

        $this->info("📊 Memulai import master data...");
        
        // Hapus data lama
        $this->database->getReference('estates')->remove();
        $this->info("🗑️ Data lama dihapus");
        
        $count = 0;
        
        foreach ($data as $estateName => $divisiList) {
            $estateData = [
                'nama' => $estateName,
                'divisi' => $divisiList,
                'created_at' => now()->toISOString()
            ];
            
            $this->database->getReference('estates')->push($estateData);
            $count++;
            
            $this->info("✅ Estate: {$estateName} - " . count($divisiList) . " divisi");
        }
        
        $this->info("");
        $this->info("✅ IMPORT SELESAI!");
        $this->info("📊 Total Estate: {$count}");
        
        return 0;
    }
}