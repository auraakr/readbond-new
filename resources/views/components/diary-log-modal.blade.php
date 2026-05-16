{{-- Diary Log Modal --}}
<div id="diary-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur-sm px-4">
    <div class="bg-[#2c3440] rounded-xl w-full max-w-2xl shadow-2xl max-h-[90vh] overflow-hidden flex flex-col">
        
        {{-- Header --}}
        <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-700/50">
            <button onclick="diaryBackToSearch()" 
                    id="diary-back-button"
                    class="text-slate-400 hover:text-white transition p-1 hidden">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <h2 id="diary-modal-title" class="text-white font-bold text-xl flex-1">Add Diary Entry</h2>
            <button onclick="closeDiaryModal()" 
                    class="text-slate-400 hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Content Area --}}
        <div class="flex-1 overflow-y-auto">
            
            {{-- STEP 1: SEARCH BOOK --}}
            <div id="diary-step-search" class="p-6">
                <div class="relative mb-4">
                    <svg class="w-5 h-5 text-slate-500 absolute left-3 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input 
                        type="text" 
                        id="diary-search"
                        placeholder="Search for a book..."
                        class="w-full bg-[#14181c] border border-slate-700 text-white rounded-lg pl-10 pr-4 py-3
                               focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition
                               placeholder-slate-500 text-sm"
                    >
                </div>

                {{-- Search Results --}}
                <div id="diary-results" class="space-y-2 hidden">
                    {{-- Results populated by JS --}}
                </div>

                {{-- Empty State --}}
                <div id="diary-empty" class="text-center py-12 text-slate-500">
                    <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <p class="text-sm">Start typing to search for books...</p>
                </div>
            </div>

            {{-- STEP 2: DIARY FORM --}}
            <div id="diary-step-form" class="hidden">
                <div class="p-6">
                    {{-- Selected Book Display --}}
                    <div class="flex gap-4 mb-6">
                        <div class="flex-shrink-0">
                            <img id="diary-selected-cover" 
                                 src="" 
                                 alt="" 
                                 class="w-24 h-36 object-cover rounded-lg shadow-lg">
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 id="diary-selected-title" class="text-white text-lg font-bold mb-1"></h3>
                            <p id="diary-selected-author" class="text-slate-400 text-sm mb-3"></p>
                            <p id="diary-selected-year" class="text-slate-500 text-xs"></p>
                        </div>
                    </div>

                    <form id="diary-form" action="{{ route('diary.store') }}" method="POST" class="space-y-5">
                        @csrf
                        <input type="hidden" name="book_id" id="diary-book-id">

                        {{-- Reading Date --}}
                        <div>
                            <label class="text-slate-400 text-xs uppercase tracking-wide mb-2 block font-semibold">
                                Reading Date
                            </label>
                            <input type="date" 
                                   name="read_date" 
                                   value="{{ date('Y-m-d') }}"
                                   max="{{ date('Y-m-d') }}"
                                   required
                                   class="w-full bg-[#14181c] border border-slate-700 text-white text-sm rounded-lg
                                          px-4 py-2.5 outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500
                                          transition">
                        </div>

                        {{-- Pages Read --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-slate-400 text-xs uppercase tracking-wide mb-2 block font-semibold">
                                    Pages Read Today
                                </label>
                                <input type="number" 
                                       name="pages_read" 
                                       min="0"
                                       placeholder="e.g., 25"
                                       class="w-full bg-[#14181c] border border-slate-700 text-white text-sm rounded-lg
                                              px-4 py-2.5 outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500
                                              placeholder-slate-600 transition">
                            </div>
                            <div>
                                <label class="text-slate-400 text-xs uppercase tracking-wide mb-2 block font-semibold">
                                    Current Page
                                </label>
                                <input type="number" 
                                       name="current_page" 
                                       min="0"
                                       placeholder="e.g., 125"
                                       class="w-full bg-[#14181c] border border-slate-700 text-white text-sm rounded-lg
                                              px-4 py-2.5 outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500
                                              placeholder-slate-600 transition">
                            </div>
                        </div>

                        {{-- Mood Selection --}}
                        <div>
                            <label class="text-slate-400 text-xs uppercase tracking-wide mb-3 block font-semibold">
                                How are you feeling?
                            </label>
                            <div class="grid grid-cols-5 gap-2">
                                @foreach([
                                    'happy' => '😊',
                                    'excited' => '🤩',
                                    'calm' => '😌',
                                    'sad' => '😢',
                                    'thoughtful' => '🤔'
                                ] as $mood => $emoji)
                                <label class="cursor-pointer">
                                    <input type="radio" 
                                           name="mood" 
                                           value="{{ $mood }}" 
                                           class="hidden peer">
                                    <div class="flex flex-col items-center justify-center gap-2 p-3
                                                bg-[#14181c] border-2 border-slate-700 rounded-lg
                                                peer-checked:bg-purple-600/20 peer-checked:border-purple-500
                                                hover:border-slate-600 transition-all cursor-pointer">
                                        <span class="text-2xl">{{ $emoji }}</span>
                                        <span class="text-[10px] text-slate-400 peer-checked:text-white uppercase tracking-wider font-semibold">
                                            {{ $mood }}
                                        </span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div>
                            <label class="text-slate-400 text-xs uppercase tracking-wide mb-2 block font-semibold">
                                Your thoughts today...
                            </label>
                            <textarea name="notes" 
                                      rows="4" 
                                      placeholder="What did you read today? How did it make you feel? Any interesting quotes or moments?"
                                      class="w-full bg-[#14181c] border border-slate-700 text-white text-sm rounded-lg
                                             px-4 py-3 resize-none outline-none focus:ring-2 focus:ring-purple-500
                                             placeholder-slate-600 transition"></textarea>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Footer / Actions --}}
        <div id="diary-form-footer" class="hidden border-t border-slate-700/50 px-6 py-4 bg-[#1a1d24]">
            <button type="submit" 
                    form="diary-form"
                    class="w-full py-3 bg-purple-600 hover:bg-purple-500 text-white text-sm font-bold
                           rounded-lg transition-all hover:scale-[1.02] active:scale-[0.98]
                           shadow-lg shadow-purple-600/20">
                Save Entry
            </button>
        </div>
    </div>
</div>

<script>
let diarySearchTimer = null;
let selectedDiaryBookId = null;

// Open modal with search
function openDiaryModal() {
    const modal = document.getElementById('diary-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.getElementById('diary-step-search').classList.remove('hidden');
    document.getElementById('diary-step-form').classList.add('hidden');
    document.getElementById('diary-form-footer').classList.add('hidden');
    document.getElementById('diary-back-button').classList.add('hidden');
    document.getElementById('diary-modal-title').textContent = 'Add Diary Entry';
    resetDiaryForm();
    document.getElementById('diary-search').focus();
}

// Close modal
function closeDiaryModal() {
    const modal = document.getElementById('diary-modal');
    modal.classList.remove('flex');
    modal.classList.add('hidden');
    resetDiaryForm();
}

// Reset form
function resetDiaryForm() {
    document.getElementById('diary-search').value = '';
    document.getElementById('diary-results').innerHTML = '';
    document.getElementById('diary-results').classList.add('hidden');
    document.getElementById('diary-empty').classList.remove('hidden');
    document.getElementById('diary-form').reset();
    selectedDiaryBookId = null;
}

// Search books
document.getElementById('diary-search')?.addEventListener('input', function() {
    clearTimeout(diarySearchTimer);
    const query = this.value.trim();

    if (query.length < 2) {
        document.getElementById('diary-results').classList.add('hidden');
        document.getElementById('diary-empty').classList.remove('hidden');
        return;
    }

    diarySearchTimer = setTimeout(async () => {
        try {
            const res = await fetch(`{{ route('books.autocomplete') }}?q=${encodeURIComponent(query)}`);
            const books = await res.json();

            const resultsDiv = document.getElementById('diary-results');
            const emptyDiv = document.getElementById('diary-empty');

            if (!books.length) {
                resultsDiv.innerHTML = '<div class="text-center py-8 text-slate-500 text-sm">No books found. Try a different search term.</div>';
                resultsDiv.classList.remove('hidden');
                emptyDiv.classList.add('hidden');
                return;
            }

            resultsDiv.innerHTML = books.map(book => `
                <button type="button" 
                        onclick="selectDiaryBook(${book.id || 'null'}, '${book.external_id}', '${book.title.replace(/'/g, "\\'")}', '${(book.author || 'Unknown').replace(/'/g, "\\'")}', null, ${book.cover ? `'${book.cover}'` : 'null'})"
                        class="w-full flex items-center gap-4 p-3 text-left bg-[#14181c] hover:bg-[#1a1d24]
                               border border-slate-700/50 rounded-lg transition group">
                    ${book.cover 
                        ? `<img src="${book.cover}" class="w-12 h-18 object-cover rounded shadow-lg shrink-0 group-hover:scale-105 transition-transform">` 
                        : `<div class="w-12 h-18 bg-slate-800 rounded shrink-0 flex items-center justify-center">
                             <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                             </svg>
                           </div>`
                    }
                    <div class="min-w-0 flex-1">
                        <p class="text-white text-sm font-semibold mb-0.5 group-hover:text-purple-400 transition-colors">
                            ${book.title}
                        </p>
                        <p class="text-slate-400 text-xs">${book.author || 'Unknown Author'}</p>
                    </div>
                </button>
            `).join('');

            resultsDiv.classList.remove('hidden');
            emptyDiv.classList.add('hidden');
        } catch (e) {
            console.error('Search error:', e);
            document.getElementById('diary-results').innerHTML = 
                '<div class="text-center py-8 text-red-400 text-sm">Error loading results. Please try again.</div>';
        }
    }, 300);
});

// Select book
async function selectDiaryBook(bookId, externalId, title, author, year = null, cover = null) {
    selectedDiaryBookId = bookId;

    // If book is from API, ensure it exists in database
    if (!bookId || bookId === 'null') {
        try {
            await fetch(`{{ url('books') }}/${externalId}`);
        } catch (e) {
            console.warn('Error creating book:', e);
        }
    }

    // Update form
    document.getElementById('diary-book-id').value = bookId && bookId !== 'null' ? bookId : externalId;
    document.getElementById('diary-selected-title').textContent = title;
    document.getElementById('diary-selected-author').textContent = author;
    document.getElementById('diary-selected-year').textContent = year ? year : '';
    
    const coverImg = document.getElementById('diary-selected-cover');
    if (cover) {
        coverImg.src = cover;
        coverImg.classList.remove('hidden');
    } else {
        coverImg.src = '';
        coverImg.classList.add('hidden');
    }

    // Switch views
    document.getElementById('diary-step-search').classList.add('hidden');
    document.getElementById('diary-step-form').classList.remove('hidden');
    document.getElementById('diary-form-footer').classList.remove('hidden');
    document.getElementById('diary-back-button').classList.remove('hidden');
    document.getElementById('diary-modal-title').textContent = 'Log Your Reading';

    resetDiaryForm();
}

// Back to search
function diaryBackToSearch() {
    document.getElementById('diary-step-search').classList.remove('hidden');
    document.getElementById('diary-step-form').classList.add('hidden');
    document.getElementById('diary-form-footer').classList.add('hidden');
    document.getElementById('diary-back-button').classList.add('hidden');
    document.getElementById('diary-modal-title').textContent = 'Add Diary Entry';
    selectedDiaryBookId = null;
    document.getElementById('diary-search').focus();
}

// Form submission
document.getElementById('diary-form')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.querySelector('button[form="diary-form"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Saving...';
    submitBtn.disabled = true;
    
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
            closeDiaryModal();
            window.location.reload(); // Refresh to show new entry
        } else {
            const errorData = await res.json();
            alert('⚠️ ' + (errorData.message || 'Error saving diary entry'));
        }
    } catch (e) {
        console.error('Form submission error:', e);
        alert('⚠️ An error occurred. Please try again.');
    } finally {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    }
});

// Close on background click
document.getElementById('diary-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeDiaryModal();
});

// Keyboard shortcut: Escape to close
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('diary-modal').classList.contains('hidden')) {
        closeDiaryModal();
    }
});
</script>

<style>
/* Smooth transitions for modal */
#diary-modal {
    animation: fadeIn 0.2s ease-out;
}

#diary-modal > div {
    animation: slideUp 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

/* Custom scrollbar */
#diary-step-search::-webkit-scrollbar,
#diary-step-form::-webkit-scrollbar {
    width: 8px;
}

#diary-step-search::-webkit-scrollbar-track,
#diary-step-form::-webkit-scrollbar-track {
    background: #14181c;
}

#diary-step-search::-webkit-scrollbar-thumb,
#diary-step-form::-webkit-scrollbar-thumb {
    background: #2c3440;
    border-radius: 4px;
}

#diary-step-search::-webkit-scrollbar-thumb:hover,
#diary-step-form::-webkit-scrollbar-thumb:hover {
    background: #394050;
}
</style>