<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Menampilkan daftar user beserta statistik ringkas
     */
    public function index(Request $request)
    {
        // Fitur Pencarian User berdasarkan Nama, Username, atau Email
        $search = $request->input('search');
        
        $usersQuery = User::query()->where('role', '!=', 'admin'); // Pengecualian agar admin tidak ikut terdaftar

        if ($search) {
            $usersQuery->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('username', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Pagination 10 user per halaman
        $users = $usersQuery->latest()->paginate(10)->withQueryString();

        // Hitung statistik untuk komponen widget atas
        $stats = [
            'total_users' => User::where('role', '!=', 'admin')->count(),
            'new_this_month' => User::where('role', '!=', 'admin')->where('created_at', '>=', now()->startOfMonth())->count(),
        ];

        return view('admin.users.index', compact('users', 'stats', 'search'));
    }

    public function show($id)
    {
        // Ambil data user beserta hitungan total relasinya secara efisien
        $user = User::withCount(['reviews', 'collections', 'followers'])->findOrFail($id);
        
        return view('admin.users.show', compact('user'));
    }
    
    /**
     * Menghapus atau memblokir akun user dari sistem
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Proteksi tambahan agar tidak salah menghapus sesama admin
        if ($user->role === 'admin') {
            return redirect()->back()->with('error', 'Tidak dapat menghapus akun Administrator.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Akun pengguna berhasil dihapus dari sistem.');
    }
}