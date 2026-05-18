{{-- Global Reading Log Modal --}}
<div id="global-reading-log-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm px-4">
    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 w-full max-w-md shadow-2xl max-h-[90vh] overflow-y-auto">
        
        {{-- STEP 1: SEARCH BOOK --}}
        <div id="step-search" class="space-y-4">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-white font-bold text-lg">Pilih Buku</h3>
                <button onclick="closeReadingLogModal()" class="text-slate-500 hover:text-white transition">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>

            <div class="relative">
                <x-heroicon-o-magnifying-glass class="w-5 h-5 text-slate-400 absolute left-3 top-3 pointer-events-none" />
                <input 
                    type="text" 
                    id="reading-log-search"
                    placeholder="Cari judul atau penulis..."
                    class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg pl-10 pr-4 py-2.5
                           focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition
                           placeholder-slate-600"
                >
            </div>

            {{-- Search Results --}}
            <div id="reading-log-results" class="space-y-2 max-h-64 overflow-y-auto hidden">
                {{-- Results will be populated by JavaScript --}}
            </div>

            {{-- Empty State --}}
            <div id="reading-log-empty" class="text-center py-8 text-slate-500">
                <x-heroicon-o-magnifying-glass class="w-12 h-12 mx-auto mb-3 opacity-40" />
                <p class="text-sm">Mulai ketik untuk mencari buku...</p>
            </div>
        </div>

        {{-- STEP 2: READING LOG FORM --}}
        <div id="step-form" class="space-y-4 hidden">
            <div class="flex items-center gap-3 mb-5">
                <button onclick="readingLogBackToSearch()" class="text-slate-400 hover:text-white transition p-1">
                    <x-heroicon-o-arrow-left class="w-5 h-5" />
                </button>
                <h3 class="text-white font-bold text-lg flex-1">Reading Log</h3>
                <button onclick="closeReadingLogModal()" class="text-slate-500 hover:text-white transition">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>

            {{-- Selected Book Info --}}
            <div class="flex flex-col">
                <img id="selected-book-cover" src="" alt="" class="w-10 h-14 object-cover rounded">
                <div class="">
                    <div id="selected-book-info" class="flex items-center gap-3 p-3 bg-slate-900 border border-slate-700 rounded-lg">
                        <div class="flex-1 min-w-0">
                            <p id="selected-book-title" class="text-white text-sm font-semibold truncate"></p>
                            <p id="selected-book-author" class="text-slate-400 text-xs truncate"></p>
                        </div>
                    </div>
                    <form id="reading-log-form" action="" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="text-slate-300 text-sm font-medium mb-2 block">Status Baca</label>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach(['want_to_read' => 'Ingin Baca', 'reading' => 'Sedang Baca', 'finished' => 'Selesai'] as $val => $label)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="status" value="{{ $val }}" class="hidden peer" required>
                                        <span class="block px-3 py-2.5 bg-slate-900 border border-slate-700 rounded-lg
                                                text-slate-300 text-xs text-center font-medium
                                                peer-checked:bg-purple-600 peer-checked:border-purple-500 peer-checked:text-white
                                                transition cursor-pointer">
                                            {{ $label }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-slate-400 text-xs mb-1 block">Mulai Baca</label>
                                <input type="date" name="started_at"
                                    class="w-full bg-slate-900 border border-slate-700 text-white text-sm rounded-lg
                                            px-3 py-2 outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500
                                            transition">
                            </div>
                            <div>
                                <label class="text-slate-400 text-xs mb-1 block">Selesai Baca</label>
                                <input type="date" name="finished_at"
                                    class="w-full bg-slate-900 border border-slate-700 text-white text-sm rounded-lg
                                            px-3 py-2 outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500
                                            transition">
                            </div>
                        </div>

                        {{-- Review Section (Only for Finished) --}}
                        <div id="review-section" class="hidden space-y-3">
                            <label class="text-slate-300 text-sm font-medium block">Review Buku</label>
                            
                            <div>
                                <label class="text-slate-400 text-xs mb-2 block">Rating</label>
                                <div class="flex gap-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <label class="cursor-pointer group">
                                            <input type="radio" name="rating" value="{{ $i }}" class="hidden peer">
                                            <span class="inline-block peer-checked:text-yellow-400 text-slate-600 group-hover:text-yellow-300 transition">
                                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            </span>
                                        </label>
                                    @endfor
                                </div>
                            </div>

                            <div>
                                <label class="text-slate-400 text-xs mb-2 block">Review</label>
                                <textarea name="review" rows="3" placeholder="Bagaimana pendapatmu tentang buku ini?"
                                        class="w-full bg-slate-900 border border-slate-700 text-white text-sm rounded-lg
                                                px-3 py-2 resize-none outline-none focus:ring-2 focus:ring-purple-500
                                                placeholder-slate-600 transition"></textarea>
                            </div>

                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_liked" class="w-4 h-4 rounded bg-slate-900 border-slate-700 
                                                                               text-purple-600 focus:ring-purple-500 transition">
                                <span class="text-slate-300 text-sm">Saya suka buku ini</span>
                            </label>
                        </div>

                        <button type="submit"
                                class="w-full py-2.5 bg-purple-600 hover:bg-purple-500 text-white text-sm
                                    font-semibold rounded-lg transition">
                            Simpan Reading Log
                        </button>
                    </form>
                </div>
            </div>
            
            
        </div>
    </div>
</div>

<script>
let selectedBookId = null;
let readingLogSearchTimer = null;

// Open modal with pre-selected book (for book detail page)
function openReadingLogModalWithBook(bookId, externalId, title, author, cover = null) {
    selectBookForReadingLog(bookId, externalId, title, author, cover);
    document.getElementById('global-reading-log-modal').classList.replace('hidden', 'flex');
}

// Open modal with search (for other pages)
function openReadingLogModal() {
    const modal = document.getElementById('global-reading-log-modal');
    modal.classList.replace('hidden', 'flex');
    document.getElementById('step-search').classList.remove('hidden');
    document.getElementById('step-form').classList.add('hidden');
    selectedBookId = null;
    document.getElementById('reading-log-search').value = '';
    document.getElementById('reading-log-results').innerHTML = '';
    document.getElementById('reading-log-results').classList.add('hidden');
    document.getElementById('reading-log-empty').classList.remove('hidden');
    document.getElementById('reading-log-search').focus();
}

// Close modal
function closeReadingLogModal() {
    document.getElementById('global-reading-log-modal').classList.replace('flex', 'hidden');
    document.getElementById('reading-log-search').value = '';
    document.getElementById('reading-log-results').innerHTML = '';
    document.getElementById('reading-log-results').classList.add('hidden');
    document.getElementById('reading-log-empty').classList.remove('hidden');
}

// Search books
document.getElementById('reading-log-search')?.addEventListener('input', function() {
    clearTimeout(readingLogSearchTimer);
    const query = this.value.trim();

    if (query.length < 2) {
        document.getElementById('reading-log-results').classList.add('hidden');
        document.getElementById('reading-log-empty').classList.remove('hidden');
        return;
    }

    readingLogSearchTimer = setTimeout(async () => {
        try {
            const res = await fetch(`{{ route('books.autocomplete') }}?q=${encodeURIComponent(query)}`);
            const books = await res.json();

            const resultsDiv = document.getElementById('reading-log-results');
            const emptyDiv = document.getElementById('reading-log-empty');

            if (!books.length) {
                resultsDiv.classList.add('hidden');
                emptyDiv.classList.remove('hidden');
                return;
            }

            resultsDiv.innerHTML = books.map(book => `
                <button type="button" 
                        onclick="selectBookForReadingLog(${book.id || 'null'}, '${book.external_id}', '${book.title.replace(/'/g, "\\'")}', '${(book.author || 'Unknown').replace(/'/g, "\\'")}'${book.cover ? `, '${book.cover}'` : ''})"
                        class="w-full flex items-center gap-3 p-3 text-left bg-slate-900 hover:bg-slate-700 
                               border border-slate-700 rounded-lg transition">
                    ${book.cover ? `<img src="${book.cover}" class="w-10 h-14 object-cover rounded shrink-0">` : `<div class="w-10 h-14 bg-slate-800 rounded shrink-0 flex items-center justify-center text-slate-600">...</div>`}
                    <div class="min-w-0 flex-1">
                        <p class="text-white text-sm font-medium truncate">${book.title}</p>
                        <p class="text-slate-400 text-xs truncate">${book.author || 'Unknown'}</p>
                    </div>
                </button>
            `).join('');

            resultsDiv.classList.remove('hidden');
            emptyDiv.classList.add('hidden');
        } catch (e) {
            console.error('Search error:', e);
        }
    }, 300);
});

// Select book
async function selectBookForReadingLog(bookId, externalId, title, author, cover = null) {
    selectedBookId = externalId;
    let finalBookId = bookId;

    // If book is from API (no database ID), fetch it to create it in database
    if (!finalBookId || finalBookId === 'null') {
        try {
            // This endpoint will create the book if it doesn't exist
            await fetch(`{{ url('books') }}/${externalId}`);
            // After fetching, the book should exist in the database
            // For the reading log, we'll use the external_id and let the form handle it
        } catch (e) {
            console.warn('Error creating book:', e);
        }
    }

    // Use book ID if available, otherwise use external_id
    const routeId = finalBookId && finalBookId !== 'null' ? finalBookId : externalId;
    
    // Update form action
    document.getElementById('reading-log-form').action = `{{ route('books.reading-log', ':id') }}`.replace(':id', routeId);

    // Show selected book info
    document.getElementById('selected-book-title').textContent = title;
    document.getElementById('selected-book-author').textContent = author;
    if (cover) {
        document.getElementById('selected-book-cover').src = cover;
    } else {
        document.getElementById('selected-book-cover').src = '';
    }

    // Switch to form step
    document.getElementById('step-search').classList.add('hidden');
    document.getElementById('step-form').classList.remove('hidden');

    // Reset form
    document.getElementById('reading-log-form').reset();
}

// Back to search
function readingLogBackToSearch() {
    document.getElementById('step-search').classList.remove('hidden');
    document.getElementById('step-form').classList.add('hidden');
    selectedBookId = null;
    document.getElementById('reading-log-search').focus();
}

// Toggle review section based on status
document.querySelectorAll('input[name="status"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const reviewSection = document.getElementById('review-section');
        if (this.value === 'finished') {
            reviewSection.classList.remove('hidden');
        } else {
            reviewSection.classList.add('hidden');
            // Clear review fields when not finished
            document.querySelector('input[name="rating"]').checked = false;
            document.querySelector('textarea[name="review"]').value = '';
            document.querySelector('input[name="is_liked"]').checked = false;
        }
    });
});

// Handle form submission
document.getElementById('reading-log-form')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    try {
        const formData = new FormData(this);
        const res = await fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        });

        if (res.ok) {
            closeReadingLogModal();
            alert('Reading Log berhasil disimpan!');
        } else {
            const errorData = await res.json();
            alert(errorData.message || 'Terjadi kesalahan saat menyimpan Reading Log');
        }
    } catch (e) {
        console.error('Form submission error:', e);
        alert('Terjadi kesalahan');
    }
});

// Close on background click
document.getElementById('global-reading-log-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeReadingLogModal();
});
</script>