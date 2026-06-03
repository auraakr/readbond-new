<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookClub;
use App\Models\BookClubDiscussion;
use App\Models\BookClubPost;
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

        $clubs = $query->latest()
            ->where('visibility', 'public')
            ->get();

        return view('clubs.index', compact('clubs'));
    }

    // Halaman Detail / Home kepunyaan Klub (Gambar 2)
    public function show($slug)
    {
        // 1. Ambil data club berdasarkan slug beserta eager load relasi currentBook
        // Eager loading relasi members dan books sekaligus agar query ringan
        $club = BookClub::with(['members', 'books' => function($query) {
            $query->withPivot('created_at')->orderBy('book_club_books.created_at', 'desc');
        }])->where('slug', $slug)->firstOrFail();

        // Data untuk looping list di pop-up modal buku
        $allBooks = Book::orderBy('title', 'asc')->get();

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
        return view('clubs.show', compact('club', 'allBooks', 'isModerator', 'isMember', 'discussions'));
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
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Tambahkan validasi file agar aman
            'allow_member_add_book' => 'nullable|boolean',
            'allow_member_add_discussion' => 'nullable|boolean',
        ]);

        // 1. Tampung data text ke dalam array
        $updateData = [
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'rules' => $request->rules,
            'visibility' => $request->visibility,
            'allow_member_add_book' => $request->has('allow_member_add_book'),
            'allow_member_add_discussion' => $request->has('allow_member_add_discussion'),
        ];

        // 2. Jika ada file cover baru, masukkan ke dalam array $updateData
        if ($request->hasFile('cover_image')) {
            // Opsional: Hapus cover lama dari storage jika ingin hemat penyimpanan
            if ($club->cover_image) {
                Storage::disk('public')->delete($club->cover_image);
            }
            
            $updateData['cover_image'] = $request->file('cover_image')->store('club_covers', 'public');
        }

        // 3. Eksekusi update secara bersamaan
        $club->update($updateData);

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

    public function createDiscussion($clubSlug)
    {
        $club = BookClub::where('slug', $clubSlug)->firstOrFail();
        
        $isModerator = $club->members()->where('user_id', Auth::id())->where('book_club_members.role', 'moderator')->exists();
        if (!$club->allow_member_add_discussion && !$isModerator) {
            return redirect()->route('clubs.show', $club->slug)
                            ->with('error', 'Hanya moderator yang dapat membuat diskusi baru.');
        }

        // Ambil semua buku untuk pilihan drop-down (bisa ditambah limit atau pagination jika sudah banyak)
        $books = Book::orderBy('title', 'asc')->get();

        return view('clubs.discussion.create', compact('club', 'books'));
    }

    public function storeDiscussion(Request $request, $clubSlug)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'book_id' => 'nullable|exists:books,id', // Validasi optional, harus ada di tabel books jika diisi
        ]);

        $club = BookClub::where('slug', $clubSlug)->firstOrFail();

        // Buat Discussion Thread beserta book_id (jika dipilih)
        $discussion = BookClubDiscussion::create([
            'book_club_id' => $club->id,
            'user_id' => Auth::id(),
            'title' => $request->title,
            'book_id' => $request->book_id, // Menyimpan relasi buku (bisa bernilai null)
        ]);

        // Buat data Post Pertama
        BookClubPost::create([
            'discussion_id' => $discussion->id,
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        return redirect()->route('clubs.discussion.show', [$club->slug, $discussion->id])
                        ->with('success', 'Topik diskusi berhasil diterbitkan!');
    }

    // 3. SHOW DISCUSSION (Tetap pakai Slug)
    public function showDiscussion($clubSlug, $discussionId)
    {
        $club = BookClub::where('slug', $clubSlug)->firstOrFail();
        
        // 1. Ambil data diskusi aktif saat ini beserta relasi isinya
        $discussion = BookClubDiscussion::with(['user', 'posts.user'])
            ->where('book_club_id', $club->id)
            ->findOrFail($discussionId);

        // 2. Ambil 5 daftar topik diskusi lainnya di klub yang sama untuk dipasang di sidebar
        $otherDiscussions = BookClubDiscussion::with('user')
            ->withCount('posts')
            ->where('book_club_id', $club->id)
            ->orderBy('updated_at', 'desc')
            ->take(5) // Ambil 5 topik teratas/terbaru saja
            ->get();

        $isMember = Auth::check() ? $club->members()->where('user_id', Auth::id())->exists() : false;

        return view('clubs.discussion.show', compact('club', 'discussion', 'otherDiscussions', 'isMember'));
    }

    // 4. STORE POST / REPLY (Ubah dari ID ke Slug)
    public function storePost(Request $request, $clubSlug, $discussionId)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        // Cari pakai slug
        $club = BookClub::where('slug', $clubSlug)->firstOrFail();
        
        $isMember = $club->members()->where('user_id', Auth::id())->exists();
        if (!$isMember) {
            return back()->with('error', 'Anda harus menjadi anggota klub untuk membalas diskusi.');
        }

        BookClubPost::create([
            'discussion_id' => $discussionId,
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        return back()->with('success', 'Balasan Anda telah diposting!');
    }

    public function addBook(Request $request, $clubSlug)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'status' => 'required|in:reading,completed,plan_to_read'
        ]);

        $club = BookClub::where('slug', $clubSlug)->firstOrFail();

        // Validasi Hak Akses: Cek apakah member biasa diizinkan menambah buku berdasarkan setting klub
        $isModerator = $club->members()->where('user_id', Auth::id())->where('book_club_members.role', 'moderator')->exists();
        if (!$club->allow_member_add_book && !$isModerator) {
            return back()->with('error', 'Hanya moderator yang dapat menambahkan buku bersama di klub ini.');
        }

        // Cek apakah buku sudah ada di daftar dengan status tersebut
        $exists = $club->books()->where('book_id', $request->book_id)->wherePivot('status', $request->status)->exists();
        if ($exists) {
            return back()->with('error', 'Buku ini sudah terdaftar di dalam list dengan status yang sama.');
        }

        // Hubungkan buku ke dalam tabel pivot book_club_books
        $club->books()->attach($request->book_id, [
            'status' => $request->status,
            'added_by' => Auth::id(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return back()->with('success', 'Buku berhasil ditambahkan ke daftar Baca Bersama!');
    }

    // ─── AXIS: UPDATE STATUS BACA BUKU (Contoh: Menandai Selesai) ───
    public function updateBookStatus(Request $request, $clubSlug, $bookId)
    {
        $request->validate([
            'status' => 'required|in:reading,completed,plan_to_read'
        ]);

        $club = BookClub::where('slug', $clubSlug)->firstOrFail();
        
        // Fitur update status umumnya dibatasi hanya untuk jajaran Moderator
        $isModerator = $club->members()->where('user_id', Auth::id())->where('book_club_members.role', 'moderator')->exists();
        if (!$isModerator) {
            return back()->with('error', 'Hanya tindakan moderator yang dapat mengubah status baca bersama.');
        }

        // Update baris pivot statusnya
        $club->books()->updateExistingPivot($bookId, [
            'status' => $request->status,
            'updated_at' => now()
        ]);

        return back()->with('success', 'Status baca bersama berhasil diperbarui!');
    }
}