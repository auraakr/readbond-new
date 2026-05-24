<?php

namespace App\Http\Controllers;

use App\Models\BookClub;
use App\Models\BookClubDiscussion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookClubController extends Controller
{
    // Halaman utama: Explore & Join Book Club (Gambar 1)
    public function index(Request $request)
    {
        $query = BookClub::withCount('members');

        // Fitur pencarian klub
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $clubs = $query->latest()->get();

        return view('clubs.index', compact('clubs'));
    }

    // Halaman Detail / Home kepunyaan Klub (Gambar 2)
    public function show($slug)
    {
        // 1. Ambil data club berdasarkan slug beserta eager load relasi currentBook
        $club = BookClub::with('currentBook')->where('slug', $slug)->firstOrFail();

        // 2. Hitung jumlah anggota secara dinamis (jika belum di-handle di database/global scope)
        $club->members_count = $club->members()->count();

        // 3. Set nilai default untuk user yang belum login (guest)
        $isModerator = false;
        $isMember = false;

        // 4. Jika user sudah login, cek role mereka di club ini melalui tabel pivot
        if (Auth::check()) {
            $userClubPosition = $club->members()
                ->where('user_id', Auth::id())
                ->first();
            
            if ($userClubPosition) {
                $isMember = true;
                
                // PERBAIKAN: Berikan nama tabel secara eksplisit pada kolom 'role' 
                // agar tidak bentrok dengan kolom 'role' milik tabel users
                if ($userClubPosition->pivot->role === 'moderator') {
                    $isModerator = true;
                }
            }
        }

        // 5. Ambil data topik diskusi (Discussion Board) milik club ini
        // Serta hitung jumlah posts (posts_count) di setiap diskusi secara otomatis
        $discussions = BookClubDiscussion::where('book_club_id', $club->id)
            ->with('user') // Eager load user pembuat diskusi
            ->withCount('posts') // Menghasilkan variabel $discussion->posts_count
            ->latest()
            ->get();

        // 6. Lempar semua variabel ke dalam view show
        return view('clubs.show', compact('club', 'isModerator', 'isMember', 'discussions'));
    }
    

    // AJax/Web Route untuk Join & Leave Club (Tombol Join di Wireframe)
    public function toggleJoin($id)
    {
        $club = BookClub::findOrFail($id);
        $userId = Auth::id();

        // Jika user adalah moderator utama, dia tidak bisa keluar dari klubnya sendiri
        if ($club->moderator_id === $userId) {
            return response()->json(['success' => false, 'message' => 'Moderator tidak bisa keluar dari klub.'], 400);
        }

        $exists = $club->members()->where('user_id', $userId)->exists();

        if ($exists) {
            $club->members()->detach($userId);
            $joined = false;
        } else {
            $club->members()->attach($userId, ['role' => 'member']);
            $joined = true;
        }

        return response()->json([
            'success' => true,
            'joined' => $joined,
            'members_count' => $club->members()->count()
        ]);
    }

    public function create()
    {
        return view('clubs.create');
    }

    // Menyimpan Book Club Baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:book_clubs,name',
            'description' => 'required|string',
            'category' => 'required|string',
            'rules' => 'nullable|string',
            'cover_image' => 'nullable|image|max:2048',
            'visibility' => 'required|in:public,private',
        ]);

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('club_covers', 'public');
        }

        $club = BookClub::create([
            'moderator_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'rules' => $request->rules,
            'cover_image' => $coverPath,
            'visibility' => $request->visibility,
            'allow_member_add_book' => $request->has('allow_member_add_book'),
            'allow_member_add_discussion' => $request->has('allow_member_add_discussion'),
        ]);

        // Kreator otomatis nempel di pivot sebagai moderator pertama
        $club->members()->attach(Auth::id(), ['role' => 'moderator']);

        return redirect()->route('clubs.show', $club->slug)->with('success', 'Book Club berhasil didirikan!');
    }

    // Halaman Dashboard Setting (Hanya Bisa Diakses Moderator - Sesuai Gambar 3)
    public function edit($slug)
    {
        $club = BookClub::where('slug', $slug)->firstOrFail();

        // Security Check: Pastikan user yang login punya role 'moderator' di klub ini
        $isModerator = $club->members()
            ->where('user_id', Auth::id())
            ->where('book_club_members.role', 'moderator') // Amankan dengan nama tabel di sini
            ->exists();

        if (!$isModerator) {
            abort(403, 'Hanya moderator yang dapat mengakses pengaturan klub.');
        }

        return view('clubs.edit', compact('club'));
    }

    // Proses Update Pengaturan Utama & Hak Akses
    public function update(Request $request, $id)
    {
        $club = BookClub::findOrFail($id);
        
        // Validasi kecocokan hak akses moderator
        if (!$club->members()->where('user_id', Auth::id())->where('book_club_members.role', 'moderator')->exists()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:book_clubs,name,' . $club->id,
            'description' => 'required|string',
            'category' => 'required|string',
            'visibility' => 'required|in:public,private',
        ]);

        if ($request->hasFile('cover_image')) {
            $club->cover_image = $request->file('cover_image')->store('club_covers', 'public');
        }

        $club->update([
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'rules' => $request->rules,
            'visibility' => $request->visibility,
            'allow_member_add_book' => $request->has('allow_member_add_book'),
            'allow_member_add_discussion' => $request->has('allow_member_add_discussion'),
        ]);

        return redirect()->route('clubs.show', $club->slug)->with('success', 'Pengaturan berhasil disimpan!');
    }

    // Aksi Tambah Moderator Baru oleh Anggota Moderator Lain
    public function addModerator(Request $request, $id)
    {
        $club = BookClub::findOrFail($id);
        
        // Cek otentikasi admin/moderator lama
        if (!$club->members()->where('user_id', Auth::id())->where('book_club_members.role', 'moderator')->exists()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Ambil user id target yang mau dinaikkan pangkatnya jadi moderator
        $targetUserId = $request->user_id;

        // Update status di tabel jembatan pivot
        $club->members()->updateExistingPivot($targetUserId, ['role' => 'moderator']);

        return back()->with('success', 'Berhasil mengangkat moderator baru!');
    }
}