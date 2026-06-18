<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class FirebaseController extends Controller
{
    protected $database;
    protected $tablename = 'hasil_panen';

    protected $extraEstates = [
        // PG 1A
        'MPTE' => 'MPTA',
        'MPTA' => 'MPTE',
        
        // PG 1B
        'MBGE' => 'MBGA',
        'MBGA' => 'MBGE',
        'MBJE' => 'MBJA',
        'MBJA' => 'MBJE',
        'MKRE' => null,
        
        // PG 2
        'MRKE' => 'MRKA',
        'MRKA' => 'MRKE',
        
        // Estate tanpa pasangan
        'MAPE' => null,
        'MLGE' => null,
        'MRBE' => null,
        'MRRA' => null,
        'MRLE' => null,
    ];

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    // ========== TAMPILAN VIEW (HTML) ==========
    
    // Halaman daftar data dengan filter tanggal
    public function index(Request $request)
    {
        $allData = $this->database->getReference($this->tablename)->getValue() ?? [];
        
        $jabatan = session('jabatan');
        $userEstate = session('estate');
        $userDivisi = session('divisi');
        $userUnit = session('unit');
        
        // Ambil semua filter dari request
        $filterDivisi = $request->input('filter_divisi');
        $filterEstate = $request->input('filter_estate');
        $filterUnit = $request->input('filter_unit');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $filteredData = [];
        
        // ========== FILTER BERDASARKAN ROLE USER ==========
        
        // ADMIN: bisa lihat semua data
        if ($jabatan == 'ADMIN') {
            $filteredData = $allData;
        }
        // DIREKTUR: bisa lihat semua data dengan filter unit
        elseif ($jabatan == 'DIREKTUR') {
            $filteredData = $allData;
            
            // Direktur bisa filter unit (PENTING: filter unit harus diterapkan FIRST)
            if (!empty($filterUnit)) {
                $unitFiltered = [];
                foreach ($filteredData as $id => $item) {
                    $estateName = $item['estate'] ?? '';
                    if ($this->isEstateInUnit($estateName, $filterUnit)) {
                        $unitFiltered[$id] = $item;
                    }
                }
                $filteredData = $unitFiltered;
            }
        }
        // GENERAL MANAGER: lihat data berdasarkan unit tempat dia bekerja
        elseif ($jabatan == 'GENERAL MANAGER') {
            foreach ($allData as $id => $item) {
                $estateName = $item['estate'] ?? '';
                if ($this->isEstateInUnit($estateName, $userUnit)) {
                    $filteredData[$id] = $item;
                }
            }
        }
        // KERANI, MANAGER, ASKEP
        elseif (in_array($jabatan, ['KERANI', 'MANAGER', 'ASKEP'])) {
            $allowedEstates = [$userEstate];
            if (isset($this->extraEstates[$userEstate]) && $this->extraEstates[$userEstate] !== null) {
                $allowedEstates[] = $this->extraEstates[$userEstate];
            }
            
            foreach ($allData as $id => $item) {
                $estateName = $item['estate'] ?? '';
                if (in_array($estateName, $allowedEstates)) {
                    $filteredData[$id] = $item;
                }
            }
        }
        // ASISTEN
        elseif ($jabatan == 'ASISTEN') {
            foreach ($allData as $id => $item) {
                if (($item['divisi'] ?? '') == $userDivisi && ($item['estate'] ?? '') == $userEstate) {
                    $filteredData[$id] = $item;
                }
            }
        }
        
        // ========== FILTER ESTATE (KHUSUS ADMIN, DIREKTUR, GM) ==========
        // PENTING: Filter estate harus diterapkan SETELAH filter unit
        if (!empty($filterEstate) && in_array($jabatan, ['ADMIN', 'DIREKTUR', 'GENERAL MANAGER'])) {
            $estateFiltered = [];
            foreach ($filteredData as $id => $item) {
                if (($item['estate'] ?? '') == $filterEstate) {
                    $estateFiltered[$id] = $item;
                }
            }
            $filteredData = $estateFiltered;
        }
        
        // ========== FILTER DIVISI (SEMUA ROLE KECUALI ASISTEN) ==========
        if (!empty($filterDivisi) && $jabatan != 'ASISTEN') {
            $divisiFiltered = [];
            foreach ($filteredData as $id => $item) {
                if (isset($item['divisi']) && $item['divisi'] == $filterDivisi) {
                    $divisiFiltered[$id] = $item;
                }
            }
            $filteredData = $divisiFiltered;
        }
        
        // ========== FILTER TANGGAL ==========
        if (!empty($startDate) && !empty($endDate)) {
            $dateFiltered = [];
            foreach ($filteredData as $id => $item) {
                if (isset($item['tgl']) && $item['tgl'] >= $startDate && $item['tgl'] <= $endDate) {
                    $dateFiltered[$id] = $item;
                }
            }
            $filteredData = $dateFiltered;
        }
        
        // DEBUG: Log untuk melihat hasil filter
        \Log::info('=== FILTER DEBUG ===');
        \Log::info('Filter Unit: ' . ($filterUnit ?? 'null'));
        \Log::info('Filter Estate: ' . ($filterEstate ?? 'null'));
        \Log::info('Filter Divisi: ' . ($filterDivisi ?? 'null'));
        \Log::info('Total Data After All Filters: ' . count($filteredData));
        
        // ========== HITUNG AVAILABLE ESTATE UNTUK DROPDOWN (berdasarkan filter unit) ==========
        $availableEstates = [];
        
        if ($jabatan == 'ADMIN') {
            $estatesData = $this->database->getReference('estates')->getValue() ?? [];
            foreach ($estatesData as $estate) {
                $nama = $estate['nama'] ?? '';
                if (!empty($nama)) {
                    $availableEstates[] = $nama;
                }
            }
            sort($availableEstates);
        } elseif ($jabatan == 'DIREKTUR') {
            // Jika ada filter unit, hanya tampilkan estate dalam unit tersebut
            if (!empty($filterUnit)) {
                if ($filterUnit == 'PG 1A') {
                    $availableEstates = ['MAPE', 'MLGE', 'MPTE', 'MPTA'];
                } elseif ($filterUnit == 'PG 1B') {
                    $availableEstates = ['MBGE', 'MBGA', 'MBJE', 'MBJA', 'MKRE'];
                } elseif ($filterUnit == 'PG 2') {
                    $availableEstates = ['MRBE', 'MRRA', 'MRKE', 'MRKA', 'MRLE'];
                }
            } else {
                $estatesData = $this->database->getReference('estates')->getValue() ?? [];
                foreach ($estatesData as $estate) {
                    $nama = $estate['nama'] ?? '';
                    if (!empty($nama)) {
                        $availableEstates[] = $nama;
                    }
                }
                sort($availableEstates);
            }
        } elseif ($jabatan == 'GENERAL MANAGER') {
            if ($userUnit == 'PG 1A') {
                $availableEstates = ['MAPE', 'MLGE', 'MPTE', 'MPTA'];
            } elseif ($userUnit == 'PG 1B') {
                $availableEstates = ['MBGE', 'MBGA', 'MBJE', 'MBJA', 'MKRE'];
            } elseif ($userUnit == 'PG 2') {
                $availableEstates = ['MRBE', 'MRRA', 'MRKE', 'MRKA', 'MRLE'];
            }
            sort($availableEstates);
        }
        
        // ========== HITUNG AVAILABLE DIVISI UNTUK DROPDOWN ==========
        $availableDivisis = [];
        
        // Tentukan data sumber untuk divisi berdasarkan filter yang dipilih
        $sourceData = [];
        
        if ($jabatan == 'ADMIN' || $jabatan == 'DIREKTUR') {
            // Mulai dari semua data
            $sourceData = $allData;
            
            // Filter berdasarkan unit jika ada
            if (!empty($filterUnit)) {
                $unitFilteredTemp = [];
                foreach ($sourceData as $item) {
                    $estateName = $item['estate'] ?? '';
                    if ($this->isEstateInUnit($estateName, $filterUnit)) {
                        $unitFilteredTemp[] = $item;
                    }
                }
                $sourceData = $unitFilteredTemp;
            }
            
            // Filter berdasarkan estate jika ada
            if (!empty($filterEstate)) {
                $estateFilteredTemp = [];
                foreach ($sourceData as $item) {
                    if (($item['estate'] ?? '') == $filterEstate) {
                        $estateFilteredTemp[] = $item;
                    }
                }
                $sourceData = $estateFilteredTemp;
            }
            
        } elseif ($jabatan == 'GENERAL MANAGER') {
            foreach ($allData as $item) {
                $estateName = $item['estate'] ?? '';
                if ($this->isEstateInUnit($estateName, $userUnit)) {
                    $sourceData[] = $item;
                }
            }
            
            // Filter berdasarkan estate jika ada
            if (!empty($filterEstate)) {
                $estateFilteredTemp = [];
                foreach ($sourceData as $item) {
                    if (($item['estate'] ?? '') == $filterEstate) {
                        $estateFilteredTemp[] = $item;
                    }
                }
                $sourceData = $estateFilteredTemp;
            }
            
        } elseif (in_array($jabatan, ['KERANI', 'MANAGER', 'ASKEP'])) {
            $tempAllowedEstates = [$userEstate];
            if (isset($this->extraEstates[$userEstate]) && $this->extraEstates[$userEstate] !== null) {
                $tempAllowedEstates[] = $this->extraEstates[$userEstate];
            }
            foreach ($allData as $item) {
                $estateName = $item['estate'] ?? '';
                if (in_array($estateName, $tempAllowedEstates)) {
                    $sourceData[] = $item;
                }
            }
        }
        
        // Kumpulkan divisi dari sourceData
        foreach ($sourceData as $item) {
            $divisi = $item['divisi'] ?? '';
            if (!empty($divisi) && !in_array($divisi, $availableDivisis)) {
                $availableDivisis[] = $divisi;
            }
        }
        sort($availableDivisis);
        
        return view('panen.index', [
            'data' => $filteredData,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'filterDivisi' => $filterDivisi,
            'filterEstate' => $filterEstate,
            'filterUnit' => $filterUnit,
            'availableDivisis' => $availableDivisis,
            'availableEstates' => $availableEstates,
            'jabatan' => $jabatan,
        ]);
    }

    // Helper function untuk mengecek apakah estate termasuk dalam unit
    private function isEstateInUnit($estateName, $unit)
    {
        $unitMapping = [
            'PG 1A' => ['MAPE', 'MLGE', 'MPTE', 'MPTA'],
            'PG 1B' => ['MBGE', 'MBGA', 'MBJE', 'MBJA', 'MKRE'],
            'PG 2' => ['MRBE', 'MRRA', 'MRKE', 'MRKA', 'MRLE'],
        ];
        
        if (!isset($unitMapping[$unit])) {
            return false;
        }
        
        return in_array($estateName, $unitMapping[$unit]);
    }

    // Ambil semua estate untuk dropdown
    public function getEstates()
    {
        $estates = $this->database->getReference('estates')->getValue() ?? [];
        $result = [];
        
        foreach ($estates as $id => $estate) {
            if (isset($estate['nama'])) {
                $result[$id] = [
                    'nama' => $estate['nama'],
                    'divisi' => $estate['divisi'] ?? []
                ];
            }
        }
        
        return response()->json($result);
    }

    // Ambil divisi berdasarkan estate_id (nama estate)
    public function getDivisi($estate_id)
    {
        $estate = $this->database->getReference('estates/' . $estate_id)->getValue();
        
        if (!$estate || !isset($estate['divisi'])) {
            return response()->json([]);
        }
        
        $result = [];
        foreach ($estate['divisi'] as $divisiName) {
            $result[] = ['nama' => $divisiName];
        }
        
        return response()->json($result);
    }
    
    // Export ke Excel berdasarkan filter
    public function exportExcel(Request $request)
    {
        \Log::info('=== EXPORT EXCEL START ===');
        \Log::info('User: ' . session('username'));
        \Log::info('Jabatan: ' . session('jabatan'));
    
        $allData = $this->database->getReference($this->tablename)->getValue() ?? [];
        
        $jabatan = session('jabatan');
        $userEstate = session('estate');
        $userDivisi = session('divisi');
        $userUnit = session('unit');
        
        // Ambil filter dari request
        $filterDivisi = $request->input('filter_divisi');
        $filterEstate = $request->input('filter_estate');
        $filterUnit = $request->input('filter_unit');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $filteredData = [];
        
        // ADMIN
        if ($jabatan == 'ADMIN') {
            $filteredData = $allData;
        }
        // DIREKTUR: dengan filter unit
        elseif ($jabatan == 'DIREKTUR') {
            $filteredData = $allData;
            if (!empty($filterUnit)) {
                $unitFiltered = [];
                foreach ($filteredData as $id => $item) {
                    $estateName = $item['estate'] ?? '';
                    if ($this->isEstateInUnit($estateName, $filterUnit)) {
                        $unitFiltered[$id] = $item;
                    }
                }
                $filteredData = $unitFiltered;
            }
        }
        // GENERAL MANAGER: hanya unit sendiri
        elseif ($jabatan == 'GENERAL MANAGER') {
            foreach ($allData as $id => $item) {
                $estateName = $item['estate'] ?? '';
                if ($this->isEstateInUnit($estateName, $userUnit)) {
                    $filteredData[$id] = $item;
                }
            }
        }
        // KERANI, MANAGER, ASKEP
        elseif (in_array($jabatan, ['KERANI', 'MANAGER', 'ASKEP'])) {
            $allowedEstates = [$userEstate];
            if (isset($this->extraEstates[$userEstate]) && $this->extraEstates[$userEstate] !== null) {
                $allowedEstates[] = $this->extraEstates[$userEstate];
            }
            foreach ($allData as $id => $item) {
                $estateName = $item['estate'] ?? '';
                if (in_array($estateName, $allowedEstates)) {
                    $filteredData[$id] = $item;
                }
            }
        }
        // ASISTEN
        elseif ($jabatan == 'ASISTEN') {
            foreach ($allData as $id => $item) {
                if (($item['divisi'] ?? '') == $userDivisi && ($item['estate'] ?? '') == $userEstate) {
                    $filteredData[$id] = $item;
                }
            }
        }
        
        // Filter ESTATE (ADMIN, DIREKTUR, GM)
        if (!empty($filterEstate) && in_array($jabatan, ['ADMIN', 'DIREKTUR', 'GENERAL MANAGER'])) {
            $estateFiltered = [];
            foreach ($filteredData as $id => $item) {
                if (($item['estate'] ?? '') == $filterEstate) {
                    $estateFiltered[$id] = $item;
                }
            }
            $filteredData = $estateFiltered;
        }
        
        // Filter DIVISI
        if (!empty($filterDivisi) && $jabatan != 'ASISTEN') {
            $divisiFiltered = [];
            foreach ($filteredData as $id => $item) {
                if (($item['divisi'] ?? '') == $filterDivisi) {
                    $divisiFiltered[$id] = $item;
                }
            }
            $filteredData = $divisiFiltered;
        }
        
        // Filter TANGGAL
        if (!empty($startDate) && !empty($endDate)) {
            $dateFiltered = [];
            foreach ($filteredData as $id => $item) {
                if (isset($item['tgl']) && $item['tgl'] >= $startDate && $item['tgl'] <= $endDate) {
                    $dateFiltered[$id] = $item;
                }
            }
            $filteredData = $dateFiltered;
        }
        
        \Log::info('Jumlah data setelah filter: ' . count($filteredData));
        return $this->generateExcel($filteredData, $startDate, $endDate, $jabatan, $filterDivisi, $filterUnit, $filterEstate);
    }

    // Generate file Excel (PRIVATE METHOD)
    private function generateExcel($data, $startDate, $endDate, $jabatan = null, $filterDivisi = null, $filterUnit = null, $filterEstate = null)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Title
        $title = "LAPORAN HASIL PANEN";
        if ($startDate && $endDate) {
            $title .= "\nPeriode: " . date('d/m/Y', strtotime($startDate)) . " - " . date('d/m/Y', strtotime($endDate));
        }
        
        // Tambahkan informasi filter
        if (!empty($filterUnit)) {
            $title .= "\nFilter Unit: " . $filterUnit;
        }
        if (!empty($filterEstate)) {
            $title .= "\nFilter Estate: " . $filterEstate;
        }
        if (!empty($filterDivisi)) {
            $title .= "\nFilter Divisi: " . $filterDivisi;
        }
        
        // Tambahkan informasi user yang mengexport
        $title .= "\nDiexport oleh: " . session('username') . " (" . session('jabatan') . ")";
        if (session('unit')) {
            $title .= " | Unit: " . session('unit');
        }
        
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells('A1:Q1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Header tabel
        $headers = [
            'No', 'Tanggal', 'Estate', 'Divisi', 'Blok', 
            'Mandor', 'Kerani', 'TPH', 'Pemanen', 
            'Janjang', 'Matang', 'Mentah', 'Kurang Matang', 
            'Lewat Matang', 'Partenor Carpi', 'Buah Batu'
        ];
        
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '3', $header);
            $sheet->getStyle($column . '3')->getFont()->setBold(true);
            $sheet->getStyle($column . '3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCCCCC');
            $sheet->getStyle($column . '3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $column++;
        }
        
        // Data
        $row = 4;
        $no = 1;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['tgl'] ?? '-');
            $sheet->setCellValue('C' . $row, $item['estate'] ?? '-');
            $sheet->setCellValue('D' . $row, $item['divisi'] ?? '-');
            $sheet->setCellValue('E' . $row, $item['blok'] ?? '-');
            $sheet->setCellValue('F' . $row, $item['mandor'] ?? '-');
            $sheet->setCellValue('G' . $row, $item['kerani'] ?? '-');
            $sheet->setCellValue('H' . $row, $item['tph'] ?? '-');
            $sheet->setCellValue('I' . $row, $item['pemanen'] ?? '-');
            $sheet->setCellValue('J' . $row, $item['janjang'] ?? 0);
            $sheet->setCellValue('K' . $row, $item['matang'] ?? 0);
            $sheet->setCellValue('L' . $row, $item['mentah'] ?? 0);
            $sheet->setCellValue('M' . $row, $item['kurangmatang'] ?? 0);
            $sheet->setCellValue('N' . $row, $item['lewatmatang'] ?? 0);
            $sheet->setCellValue('O' . $row, $item['partenorcarpi'] ?? 0);
            $sheet->setCellValue('P' . $row, $item['buahbatu'] ?? 0);
            $row++;
        }
        
        // Auto size columns
        foreach (range('A', 'P') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Border untuk tabel
        if ($row > 4) {
            $sheet->getStyle('A3:P' . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }
        
        // Footer
        $footerRow = $row + 1;
        $sheet->setCellValue('A' . $footerRow, "Total Data: " . count($data));
        $sheet->mergeCells('A' . $footerRow . ':P' . $footerRow);
        $sheet->getStyle('A' . $footerRow)->getFont()->setItalic(true);
        
        // Set nama file
        $username = session('username') ?? 'unknown';
        $filterText = '';
        if (!empty($filterUnit)) $filterText .= '_Unit_' . $filterUnit;
        if (!empty($filterEstate)) $filterText .= '_Estate_' . $filterEstate;
        if (!empty($filterDivisi)) $filterText .= '_Divisi_' . $filterDivisi;
        $filename = 'Laporan_Hasil_Panen_' . $username . $filterText . '_' . date('Y-m-d_His') . '.xlsx';
        
        // Simpan ke output
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $writer->save('php://output');
        exit;
    }

    // Ambil daftar divisi yang tersedia untuk user yang login
    public function getDivisiByUser()
    {
        $jabatan = session('jabatan');
        $userEstate = session('estate');
        $userUnit = session('unit');
        
        $allData = $this->database->getReference($this->tablename)->getValue() ?? [];
        
        // Tentukan estate yang boleh dilihat user
        if ($jabatan == 'ADMIN' || $jabatan == 'DIREKTUR') {
            $allowedEstates = array_keys($this->database->getReference('estates')->getValue() ?? []);
        } elseif ($jabatan == 'GENERAL MANAGER') {
            $allowedEstates = [];
            if ($userUnit == 'PG 1A') {
                $allowedEstates = ['MAPE', 'MLGE', 'MPTE', 'MPTA'];
            } elseif ($userUnit == 'PG 1B') {
                $allowedEstates = ['MBGE', 'MBGA', 'MBJE', 'MBJA', 'MKRE'];
            } elseif ($userUnit == 'PG 2') {
                $allowedEstates = ['MRBE', 'MRRA', 'MRKE', 'MRKA', 'MRLE'];
            }
        } else {
            $allowedEstates = [$userEstate];
            if (isset($this->extraEstates[$userEstate]) && $this->extraEstates[$userEstate] !== null) {
                $allowedEstates[] = $this->extraEstates[$userEstate];
            }
        }
        
        // Kumpulkan divisi dari data yang ada
        $divisis = [];
        foreach ($allData as $item) {
            $estateName = $item['estate'] ?? '';
            $divisi = $item['divisi'] ?? '';
            
            if (in_array($estateName, $allowedEstates) && !empty($divisi) && !in_array($divisi, $divisis)) {
                $divisis[] = $divisi;
            }
        }
        
        // Urutkan divisi
        sort($divisis);
        
        return response()->json($divisis);
    }

    // ========== METHOD CREATE VIEW ==========
    
    public function createView()
    {
        $userJabatan = session('jabatan');
        $userEstate = session('estate');
        $isAdmin = ($userJabatan == 'ADMIN');
        
        // Ambil semua estate dari Firebase
        $allEstates = $this->database->getReference('estates')->getValue() ?? [];
        
        // Tentukan estate yang diizinkan berdasarkan role
        $allowedEstateNames = [];
        
        if ($isAdmin) {
            $allowedEstateNames = array_keys($allEstates);
        } else {
            // KERANI, MANAGER, ASKEP: bisa akses estate sendiri dan estate tambahan
            if (in_array($userJabatan, ['KERANI', 'MANAGER', 'ASKEP'])) {
                $allowedEstateNames[] = $userEstate;
                if (isset($this->extraEstates[$userEstate]) && $this->extraEstates[$userEstate] !== null) {
                    $allowedEstateNames[] = $this->extraEstates[$userEstate];
                }
            }
            // ASISTEN: hanya estate sendiri
            else {
                $allowedEstateNames = [$userEstate];
            }
        }
        
        // Filter estates dropdown berdasarkan yang diizinkan
        $estatesDropdown = [];
        $selectedEstateId = null;
        
        foreach ($allEstates as $id => $estate) {
            $nama = $estate['nama'] ?? '';
            if (in_array($nama, $allowedEstateNames)) {
                $estatesDropdown[$id] = [
                    'nama' => $nama,
                    'divisi' => $estate['divisi'] ?? []
                ];
                // Default pilih estate sendiri jika ada
                if (!$isAdmin && $nama == $userEstate) {
                    $selectedEstateId = $id;
                }
            }
        }
        
        // Tentukan apakah perlu menampilkan dropdown estate
        $showEstateDropdown = (count($estatesDropdown) > 1);
        
        // Jika hanya 1 estate dan belum ada selected, ambil ID-nya
        if (!$isAdmin && count($estatesDropdown) == 1 && !$selectedEstateId) {
            $selectedEstateId = array_key_first($estatesDropdown);
        }
        
        return view('panen.create', [
            'estates' => $estatesDropdown,
            'isAdmin' => $isAdmin,
            'userEstate' => $userEstate,
            'selectedEstateId' => $selectedEstateId,
            'showEstateDropdown' => $showEstateDropdown,
        ]);
    }
    
    // ========== METHOD EDIT VIEW ==========
    
    public function editView($id)
    {
        $data = $this->database->getReference($this->tablename . '/' . $id)->getValue();
        if (!$data) {
            abort(404, 'Data tidak ditemukan');
        }
        
        $userJabatan = session('jabatan');
        $userEstate = session('estate');
        $isAdmin = ($userJabatan == 'ADMIN');
        
        // Ambil semua estate dengan divisi
        $allEstates = $this->database->getReference('estates')->getValue() ?? [];
        $estatesDropdown = [];
        
        if ($isAdmin) {
            // Admin: semua estate
            foreach ($allEstates as $eid => $estate) {
                if (isset($estate['nama'])) {
                    $estatesDropdown[$eid] = [
                        'nama' => $estate['nama'],
                        'divisi' => $estate['divisi'] ?? []
                    ];
                }
            }
            $selectedEstateId = $data['estate_id'] ?? '';
        } else {
            // KERANI, MANAGER, ASKEP: bisa edit estate sendiri dan estate tambahan
            if (in_array($userJabatan, ['KERANI', 'MANAGER', 'ASKEP'])) {
                $allowedEstates = [$userEstate];
                if (isset($this->extraEstates[$userEstate]) && $this->extraEstates[$userEstate] !== null) {
                    $allowedEstates[] = $this->extraEstates[$userEstate];
                }
                
                foreach ($allEstates as $eid => $estate) {
                    $nama = $estate['nama'] ?? '';
                    if (in_array($nama, $allowedEstates)) {
                        $estatesDropdown[$eid] = [
                            'nama' => $nama,
                            'divisi' => $estate['divisi'] ?? []
                        ];
                    }
                }
                $selectedEstateId = $data['estate_id'] ?? array_key_first($estatesDropdown);
            }
            // ASISTEN: hanya estate sendiri
            elseif ($userJabatan == 'ASISTEN') {
                foreach ($allEstates as $eid => $estate) {
                    if (isset($estate['nama']) && $estate['nama'] == $userEstate) {
                        $estatesDropdown[$eid] = [
                            'nama' => $estate['nama'],
                            'divisi' => $estate['divisi'] ?? []
                        ];
                        $selectedEstateId = $eid;
                        break;
                    }
                }
            }
            // Role lain: hanya estate sendiri
            else {
                foreach ($allEstates as $eid => $estate) {
                    if (isset($estate['nama']) && $estate['nama'] == $userEstate) {
                        $estatesDropdown[$eid] = [
                            'nama' => $estate['nama'],
                            'divisi' => $estate['divisi'] ?? []
                        ];
                        $selectedEstateId = $eid;
                        break;
                    }
                }
            }
        }
        
        return view('panen.edit', [
            'id' => $id,
            'data' => $data,
            'estates' => $estatesDropdown,
            'isAdmin' => $isAdmin,
            'userEstate' => $userEstate,
            'selectedEstateId' => $selectedEstateId
        ]);
    }
    
    // ========== METHOD SHOW ==========
    
    public function show($id)
    {
        $data = $this->database->getReference($this->tablename . '/' . $id)->getValue();
        
        if (!$data) {
            abort(404, 'Data tidak ditemukan');
        }
        
        return view('panen.show', ['id' => $id, 'data' => $data]);
    }
    
    // ========== METHOD STORE ==========
    
    public function store(Request $request)
    {
        $jabatan = session('jabatan');
        $otorisasi = session('otorisasi');
        $userEstate = session('estate');
        $isAdmin = ($jabatan == 'ADMIN');

        // Cek akses input data
        if (!$isAdmin && !str_contains($otorisasi, 'input data')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menambah data!');
        }

        // Validasi input
        $request->validate([
            'tgl' => 'required|date',
            'estate_id' => 'required',
            'divisi' => 'required',
            'blok' => 'required',
            'mandor' => 'required',
            'kerani' => 'required',
            'tph' => 'required|numeric',
            'pemanen' => 'required',
            'janjang' => 'required|numeric',
            'matang' => 'required|numeric',
        ]);

        // Ambil estate_id dari form
        $estateId = $request->estate_id;
        
        // Cari nama estate berdasarkan estate_id
        $allEstates = $this->database->getReference('estates')->getValue() ?? [];
        $estateName = null;
        foreach ($allEstates as $id => $estate) {
            if ($id == $estateId) {
                $estateName = $estate['nama'] ?? null;
                break;
            }
        }
        
        if (!$estateName) {
            return redirect()->back()->with('error', 'Estate tidak valid!');
        }

        // ========== CEK AKSES ==========
        
        // ADMIN: bisa semua
        if ($isAdmin) {
            // Lanjutkan
        }
        // KERANI, MANAGER, ASKEP: bisa input ke estate sendiri dan estate tambahan
        elseif (in_array($jabatan, ['KERANI', 'MANAGER', 'ASKEP'])) {
            $allowedEstates = [$userEstate];
            if (isset($this->extraEstates[$userEstate]) && $this->extraEstates[$userEstate] !== null) {
                $allowedEstates[] = $this->extraEstates[$userEstate];
            }
            
            if (!in_array($estateName, $allowedEstates)) {
                $allowedList = implode(' atau ', $allowedEstates);
                return redirect()->back()->with('error', "Anda hanya bisa input data untuk $allowedList!");
            }
        }
        // ASISTEN: hanya estate dan divisi sendiri
        elseif ($jabatan == 'ASISTEN') {
            $userDivisi = session('divisi');
            if ($estateName != $userEstate || $request->divisi != $userDivisi) {
                return redirect()->back()->with('error', "Anda hanya bisa input data untuk estate $userEstate divisi $userDivisi!");
            }
        }
        // GENERAL MANAGER: cek unit
        elseif ($jabatan == 'GENERAL MANAGER') {
            $userUnit = session('unit');
            if (!$this->isEstateInUnit($estateName, $userUnit)) {
                $allowedEstates = [];
                if ($userUnit == 'PG 1A') {
                    $allowedEstates = ['MAPE', 'MLGE', 'MPTE', 'MPTA'];
                } elseif ($userUnit == 'PG 1B') {
                    $allowedEstates = ['MBGE', 'MBGA', 'MBJE', 'MBJA', 'MKRE'];
                } elseif ($userUnit == 'PG 2') {
                    $allowedEstates = ['MRBE', 'MRRA', 'MRKE', 'MRKA', 'MRLE'];
                }
                $allowedList = implode(', ', $allowedEstates);
                return redirect()->back()->with('error', "Anda hanya bisa input data untuk estate dalam unit $userUnit: $allowedList!");
            }
        }

        // Siapkan data untuk disimpan
        $postData = [
            'tgl' => $request->tgl,
            'estate' => $estateName,
            'estate_id' => $estateId,
            'divisi' => $request->divisi,
            'blok' => $request->blok,
            'mandor' => $request->mandor,
            'kerani' => $request->kerani,
            'tph' => $request->tph,
            'pemanen' => $request->pemanen,
            'janjang' => $request->janjang,
            'matang' => $request->matang,
            'mentah' => $request->mentah ?? 0,
            'kurangmatang' => $request->kurangmatang ?? 0,
            'lewatmatang' => $request->lewatmatang ?? 0,
            'partenorcarpi' => $request->partenorcarpi ?? 0,
            'buahbatu' => $request->buahbatu ?? 0,
            'created_at' => now()->toDateTimeString(),
            'created_by' => session('username') ?? session('estate')
        ];

        // Simpan ke Firebase
        $this->database->getReference($this->tablename)->push($postData);

        return redirect()->route('panen.index')->with('success', "Data berhasil disimpan untuk estate $estateName");
    }
    
    // ========== METHOD UPDATE ==========
    
    public function update(Request $request, $id)
    {
        $jabatan = session('jabatan');
        $otorisasi = session('otorisasi');
        $userEstate = session('estate');
        
        // Cek apakah user bisa edit/hapus
        if ($jabatan != 'ADMIN' && !str_contains($otorisasi, 'edit, hapus')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengedit data!');
        }
        
        // Ambil data yang akan diedit
        $existingData = $this->database->getReference($this->tablename . '/' . $id)->getValue();
        
        if (!$existingData) {
            return redirect()->route('panen.index')->with('error', 'Data tidak ditemukan!');
        }
        
        // Validasi akses berdasarkan role
        
        // ADMIN: bisa edit semua
        if ($jabatan == 'ADMIN') {
            // Lanjutkan
        }
        // ASISTEN: hanya bisa edit data divisi sendiri
        elseif ($jabatan == 'ASISTEN') {
            if (($existingData['divisi'] ?? '') != session('divisi')) {
                return redirect()->back()->with('error', 'Anda hanya bisa mengedit data divisi ' . session('divisi') . '!');
            }
            if (($existingData['estate'] ?? '') != $userEstate) {
                return redirect()->back()->with('error', 'Anda hanya bisa mengedit data estate ' . $userEstate . '!');
            }
        }
        // KERANI, MANAGER, ASKEP: bisa edit estate sendiri dan estate tambahan
        elseif (in_array($jabatan, ['KERANI', 'MANAGER', 'ASKEP'])) {
            $allowedEstates = [$userEstate];
            if (isset($this->extraEstates[$userEstate]) && $this->extraEstates[$userEstate] !== null) {
                $allowedEstates[] = $this->extraEstates[$userEstate];
            }
            
            $dataEstate = $existingData['estate'] ?? '';
            if (!in_array($dataEstate, $allowedEstates)) {
                $allowedList = implode(' atau ', $allowedEstates);
                return redirect()->back()->with('error', "Anda hanya bisa mengedit data untuk $allowedList!");
            }
        }
        // Role lain: hanya estate sendiri
        else {
            if (($existingData['estate'] ?? '') != $userEstate) {
                return redirect()->back()->with('error', 'Anda hanya bisa mengedit data estate ' . $userEstate . '!');
            }
        }
        
        // Ambil nama estate dari request
        $estateId = $request->estate_id;
        $allEstates = $this->database->getReference('estates')->getValue() ?? [];
        $estateName = null;
        foreach ($allEstates as $eid => $estate) {
            if ($eid == $estateId) {
                $estateName = $estate['nama'] ?? null;
                break;
            }
        }
        
        $updateData = [
            'tgl' => $request->tgl,
            'estate' => $estateName ?? $request->estate,
            'estate_id' => $request->estate_id,
            'divisi' => $request->divisi,
            'blok' => $request->blok,
            'mandor' => $request->mandor,
            'kerani' => $request->kerani,
            'tph' => $request->tph,
            'pemanen' => $request->pemanen,
            'janjang' => $request->janjang,
            'matang' => $request->matang,
            'mentah' => $request->mentah ?? 0,
            'kurangmatang' => $request->kurangmatang ?? 0,
            'lewatmatang' => $request->lewatmatang ?? 0,
            'partenorcarpi' => $request->partenorcarpi ?? 0,
            'buahbatu' => $request->buahbatu ?? 0,
            'updated_at' => now()->toDateTimeString(),
            'updated_by' => session('username') ?? session('estate')
        ];

        $this->database->getReference($this->tablename . '/' . $id)->update($updateData);

        return redirect()->route('panen.index')->with('success', 'Data berhasil diupdate');
    }

    // ========== METHOD DESTROY ==========
    
    public function destroy($id)
    {
        $jabatan = session('jabatan');
        $otorisasi = session('otorisasi');
        $userEstate = session('estate');
        
        // Cek apakah user bisa hapus
        if ($jabatan != 'ADMIN' && !str_contains($otorisasi, 'edit, hapus')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus data!');
        }
        
        // Ambil data yang akan dihapus
        $existingData = $this->database->getReference($this->tablename . '/' . $id)->getValue();
        
        if (!$existingData) {
            return redirect()->route('panen.index')->with('error', 'Data tidak ditemukan!');
        }
        
        // Validasi akses berdasarkan role
        
        // ADMIN: bisa hapus semua
        if ($jabatan == 'ADMIN') {
            // Lanjutkan
        }
        // ASISTEN: hanya bisa hapus data divisi sendiri
        elseif ($jabatan == 'ASISTEN') {
            if (($existingData['divisi'] ?? '') != session('divisi')) {
                return redirect()->back()->with('error', 'Anda hanya bisa menghapus data divisi ' . session('divisi') . '!');
            }
            if (($existingData['estate'] ?? '') != $userEstate) {
                return redirect()->back()->with('error', 'Anda hanya bisa menghapus data estate ' . $userEstate . '!');
            }
        }
        // KERANI, MANAGER, ASKEP: bisa hapus estate sendiri dan estate tambahan
        elseif (in_array($jabatan, ['KERANI', 'MANAGER', 'ASKEP'])) {
            $allowedEstates = [$userEstate];
            if (isset($this->extraEstates[$userEstate]) && $this->extraEstates[$userEstate] !== null) {
                $allowedEstates[] = $this->extraEstates[$userEstate];
            }
            
            $dataEstate = $existingData['estate'] ?? '';
            if (!in_array($dataEstate, $allowedEstates)) {
                $allowedList = implode(' atau ', $allowedEstates);
                return redirect()->back()->with('error', "Anda hanya bisa menghapus data untuk $allowedList!");
            }
        }
        // Role lain: hanya estate sendiri
        else {
            if (($existingData['estate'] ?? '') != $userEstate) {
                return redirect()->back()->with('error', 'Anda hanya bisa menghapus data estate ' . $userEstate . '!');
            }
        }
        
        // Hapus data dari Firebase
        $this->database->getReference($this->tablename . '/' . $id)->remove();
        
        return redirect()->route('panen.index')->with('success', 'Data berhasil dihapus!');
    }

    // ========== METHOD STATISTIK BULAN ==========
    
    public function statistikBulan(Request $request)
    {
        $jabatan = session('jabatan');
        $userEstate = session('estate');
        $userDivisi = session('divisi');
        $userUnit = session('unit');
        $username = session('username');

        $allData = $this->database->getReference($this->tablename)->getValue() ?? [];
        $filteredData = [];

        // ADMIN: lihat semua
        if ($jabatan == 'ADMIN') {
            $filteredData = $allData;
        }
        // DIREKTUR: lihat semua
        elseif ($jabatan == 'DIREKTUR') {
            $filteredData = $allData;
        }
        // GENERAL MANAGER: filter berdasarkan UNIT
        elseif ($jabatan == 'GENERAL MANAGER') {
            foreach ($allData as $id => $item) {
                $estateName = $item['estate'] ?? '';
                if ($this->isEstateInUnit($estateName, $userUnit)) {
                    $filteredData[$id] = $item;
                }
            }
        }
        // KERANI: filter berdasarkan estate sendiri dan estate tambahan
        elseif ($jabatan == 'KERANI') {
            $allowedEstates = [$userEstate];
            if (isset($this->extraEstates[$userEstate]) && $this->extraEstates[$userEstate] !== null) {
                $allowedEstates[] = $this->extraEstates[$userEstate];
            }
            
            foreach ($allData as $id => $item) {
                $estateName = $item['estate'] ?? '';
                if (in_array($estateName, $allowedEstates)) {
                    $filteredData[$id] = $item;
                }
            }
        }
        // ASKEP & MANAGER: filter berdasarkan estate sendiri dan estate tambahan
        elseif (in_array($jabatan, ['ASKEP', 'MANAGER'])) {
            $allowedEstates = [$userEstate];
            if (isset($this->extraEstates[$userEstate]) && $this->extraEstates[$userEstate] !== null) {
                $allowedEstates[] = $this->extraEstates[$userEstate];
            }
            
            foreach ($allData as $id => $item) {
                $estateName = $item['estate'] ?? '';
                if (in_array($estateName, $allowedEstates)) {
                    $filteredData[$id] = $item;
                }
            }
        }
        // ASISTEN: filter berdasarkan divisi DAN estate
        elseif ($jabatan == 'ASISTEN') {
            foreach ($allData as $id => $item) {
                if (isset($item['divisi']) && $item['divisi'] == $userDivisi 
                    && isset($item['estate']) && $item['estate'] == $userEstate) {
                    $filteredData[$id] = $item;
                }
            }
        }

        // Jika tidak ada data, return response kosong
        if (empty($filteredData)) {
            return response()->json([
                'labels' => [],
                'janjang' => [],
                'matang' => [],
                'mentah' => [],
                'total' => [
                    'janjang' => 0,
                    'matang' => 0,
                    'mentah' => 0,
                    'kurangmatang' => 0,
                    'lewatmatang' => 0,
                    'partenorcarpi' => 0,
                    'buahbatu' => 0,
                ]
            ]);
        }

        // Tentukan rentang waktu: 30 hari terakhir
        $endDate = date('Y-m-d');
        $startDate = date('Y-m-d', strtotime('-30 days'));

        // Siapkan array tanggal
        $dates = [];
        $current = strtotime($startDate);
        while ($current <= strtotime($endDate)) {
            $dateKey = date('Y-m-d', $current);
            $dates[$dateKey] = [
                'janjang' => 0,
                'matang' => 0,
                'mentah' => 0,
                'kurangmatang' => 0,
                'lewatmatang' => 0,
                'partenorcarpi' => 0,
                'buahbatu' => 0,
            ];
            $current = strtotime('+1 day', $current);
        }

        // Akumulasi data
        foreach ($filteredData as $item) {
            $tgl = $item['tgl'] ?? '';
            if ($tgl && isset($dates[$tgl])) {
                $dates[$tgl]['janjang'] += (int)($item['janjang'] ?? 0);
                $dates[$tgl]['matang'] += (int)($item['matang'] ?? 0);
                $dates[$tgl]['mentah'] += (int)($item['mentah'] ?? 0);
                $dates[$tgl]['kurangmatang'] += (int)($item['kurangmatang'] ?? 0);
                $dates[$tgl]['lewatmatang'] += (int)($item['lewatmatang'] ?? 0);
                $dates[$tgl]['partenorcarpi'] += (int)($item['partenorcarpi'] ?? 0);
                $dates[$tgl]['buahbatu'] += (int)($item['buahbatu'] ?? 0);
            }
        }

        $labels = array_keys($dates);
        $janjangData = array_column($dates, 'janjang');
        $matangData = array_column($dates, 'matang');
        $mentahData = array_column($dates, 'mentah');

        $total = [
            'janjang' => array_sum($janjangData),
            'matang' => array_sum($matangData),
            'mentah' => array_sum($mentahData),
            'kurangmatang' => array_sum(array_column($dates, 'kurangmatang')),
            'lewatmatang' => array_sum(array_column($dates, 'lewatmatang')),
            'partenorcarpi' => array_sum(array_column($dates, 'partenorcarpi')),
            'buahbatu' => array_sum(array_column($dates, 'buahbatu')),
        ];

        return response()->json([
            'labels' => $labels,
            'janjang' => $janjangData,
            'matang' => $matangData,
            'mentah' => $mentahData,
            'total' => $total,
        ]);
    }

    // Ambil divisi berdasarkan estate yang dipilih
    public function getDivisiByEstate(Request $request)
    {
        $estate = $request->input('estate');
        $allData = $this->database->getReference($this->tablename)->getValue() ?? [];
        
        $divisis = [];
        
        // Cari dari data hasil panen
        foreach ($allData as $item) {
            if (($item['estate'] ?? '') == $estate) {
                $divisi = $item['divisi'] ?? '';
                if (!empty($divisi) && !in_array($divisi, $divisis)) {
                    $divisis[] = $divisi;
                }
            }
        }
        
        // Jika tidak ada data dari hasil panen, ambil dari master estate
        if (empty($divisis)) {
            $estateData = $this->database->getReference('estates/' . $estate)->getValue();
            if ($estateData && isset($estateData['divisi'])) {
                $divisis = $estateData['divisi'];
            }
        }
        
        // Jika masih kosong, gunakan data divisi default
        if (empty($divisis)) {
            $defaultDivisis = [
                'MAPE' => ['PE01', 'PE02', 'PE03'],
                'MLGE' => ['LG01', 'LG02', 'LG03'],
                'MPTE' => ['DE01', 'DE02', 'DE03'],
                'MPTA' => ['DP01'],
                'MBGE' => ['GE01', 'GE02', 'GE03'],
                'MBGA' => ['GA01'],
                'MBJE' => ['JE01', 'JE02', 'JE03'],
                'MBJA' => ['JA01'],
                'MKRE' => ['KR01', 'KR02', 'KR03'],
                'MRBE' => ['RB01', 'RB02', 'RB03'],
                'MRRA' => ['RA01', 'RA02', 'RA03'],
                'MRKE' => ['KE01', 'KE02', 'KE03'],
                'MRKA' => ['KA01'],
                'MRLE' => ['RL01', 'RL02', 'RL03'],
            ];
            $divisis = $defaultDivisis[$estate] ?? [];
        }
        
        sort($divisis);
        return response()->json($divisis);
    }

    // Ambil semua divisi (tanpa filter)
    public function getAllDivisi(Request $request)
    {
        $allData = $this->database->getReference($this->tablename)->getValue() ?? [];
        $divisis = [];
        
        foreach ($allData as $item) {
            $divisi = $item['divisi'] ?? '';
            if (!empty($divisi) && !in_array($divisi, $divisis)) {
                $divisis[] = $divisi;
            }
        }
        
        sort($divisis);
        return response()->json($divisis);
    }

    // Ambil estate berdasarkan unit (untuk DIREKTUR)
    public function getEstatesByUnit(Request $request)
    {
        $unit = $request->input('unit');
        
        if ($unit == 'PG 1A') {
            $estates = ['MAPE', 'MLGE', 'MPTE', 'MPTA'];
        } elseif ($unit == 'PG 1B') {
            $estates = ['MBGE', 'MBGA', 'MBJE', 'MBJA', 'MKRE'];
        } elseif ($unit == 'PG 2') {
            $estates = ['MRBE', 'MRRA', 'MRKE', 'MRKA', 'MRLE'];
        } else {
            $estates = [];
        }
        
        return response()->json(['estates' => $estates]);
    }
}