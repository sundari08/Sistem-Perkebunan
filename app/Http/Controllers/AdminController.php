<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\AdminMiddleware;


class AdminController extends Controller
{
    protected $database;
    protected $tablename = 'users';
    protected $estateTable = 'estates';

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    // ========== DASHBOARD ADMIN ==========
    public function dashboard()
    {
        $totalUsers = count($this->database->getReference($this->tablename)->getValue() ?? []);
        $totalEstates = count($this->database->getReference($this->estateTable)->getValue() ?? []);
        $totalPanen = count($this->database->getReference('hasil_panen')->getValue() ?? []);
        
        return view('admin.dashboard', compact('totalUsers', 'totalEstates', 'totalPanen'));
    }

    // ========== CRUD USER ==========
    
    public function usersIndex(Request $request)
    {
        $users = $this->database->getReference($this->tablename)->getValue() ?? [];
        
        // Filter berdasarkan jabatan
        $filterJabatan = $request->input('filter_jabatan');
        if (!empty($filterJabatan)) {
            $filtered = [];
            foreach ($users as $id => $user) {
                if (($user['jabatan'] ?? '') == $filterJabatan) {
                    $filtered[$id] = $user;
                }
            }
            $users = $filtered;
        }
        
        return view('admin.users.index', [
            'users' => $users,
            'filterJabatan' => $filterJabatan,
        ]);
    }
    
    public function usersCreate()
    {
        $estates = $this->database->getReference($this->estateTable)->getValue() ?? [];
        return view('admin.users.create', ['estates' => $estates]);
    }
    
    public function usersStore(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string|min:4',
            'jabatan' => 'required|string',
        ]);
        
        $userData = [
            'username' => $request->username,
            'password' => md5($request->password),
            'jabatan' => $request->jabatan,
            'estate' => $request->estate ?? null,
            'divisi' => $request->divisi ?? null,
            'unit' => $request->unit ?? null,
            'otorisasi' => $request->otorisasi ?? '',
        ];
        
        $this->database->getReference($this->tablename)->push($userData);
        
        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan!');
    }
    
    public function usersEdit($id)
    {
        $user = $this->database->getReference($this->tablename . '/' . $id)->getValue();
        if (!$user) {
            abort(404, 'User tidak ditemukan');
        }
        $user['id'] = $id;
        
        $estates = $this->database->getReference($this->estateTable)->getValue() ?? [];
        
        return view('admin.users.edit', [
            'user' => $user,
            'estates' => $estates,
        ]);
    }
    
    public function usersUpdate(Request $request, $id)
    {
        $request->validate([
            'username' => 'required|string',
            'jabatan' => 'required|string',
        ]);
        
        $updateData = [
            'username' => $request->username,
            'jabatan' => $request->jabatan,
            'estate' => $request->estate ?? null,
            'divisi' => $request->divisi ?? null,
            'unit' => $request->unit ?? null,
            'otorisasi' => $request->otorisasi ?? '',
        ];
        
        // Update password jika diisi
        if (!empty($request->password)) {
            $updateData['password'] = md5($request->password);
        }
        
        $this->database->getReference($this->tablename . '/' . $id)->update($updateData);
        
        return redirect()->route('admin.users.index')->with('success', 'User berhasil diupdate!');
    }
    
    public function usersDestroy($id)
    {
        $this->database->getReference($this->tablename . '/' . $id)->remove();
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus!');
    }

    // ========== CRUD ESTATE ==========
    
    public function estatesIndex()
    {
        $estates = $this->database->getReference($this->estateTable)->getValue() ?? [];
        return view('admin.estates.index', ['estates' => $estates]);
    }
    
    public function estatesCreate()
    {
        return view('admin.estates.create');
    }
    
    public function estatesStore(Request $request)
    {
        $request->validate([
            'id' => 'required|string',
            'nama' => 'required|string',
            'divisi' => 'required|string',
        ]);
        
        // Parse divisi (format: DE01,DE02,DE03)
        $divisiArray = array_map('trim', explode(',', $request->divisi));
        
        $estateData = [
            'nama' => $request->nama,
            'divisi' => $divisiArray,
        ];
        
        $this->database->getReference($this->estateTable . '/' . $request->id)->set($estateData);
        
        return redirect()->route('admin.estates.index')->with('success', 'Estate berhasil ditambahkan!');
    }
    
    public function estatesEdit($id)
    {
        $estate = $this->database->getReference($this->estateTable . '/' . $id)->getValue();
        if (!$estate) {
            abort(404, 'Estate tidak ditemukan');
        }
        $estate['id'] = $id;
        $estate['divisi_string'] = implode(', ', $estate['divisi'] ?? []);
        
        return view('admin.estates.edit', ['estate' => $estate]);
    }
    
    public function estatesUpdate(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string',
            'divisi' => 'required|string',
        ]);
        
        $divisiArray = array_map('trim', explode(',', $request->divisi));
        
        $updateData = [
            'nama' => $request->nama,
            'divisi' => $divisiArray,
        ];
        
        $this->database->getReference($this->estateTable . '/' . $id)->update($updateData);
        
        return redirect()->route('admin.estates.index')->with('success', 'Estate berhasil diupdate!');
    }
    
    public function estatesDestroy($id)
    {
        $this->database->getReference($this->estateTable . '/' . $id)->remove();
        return redirect()->route('admin.estates.index')->with('success', 'Estate berhasil dihapus!');
    }
}
