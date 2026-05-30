{{-- Global Reading Log Modal --}}
<div id="global-reading-log-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm px-4">
    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 w-full max-w-3xl shadow-2xl max-h-[90vh] overflow-y-auto">

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

            <div id="reading-log-results" class="space-y-2 max-h-64 overflow-y-auto hidden"></div>

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
            <div class="flex gap-4 items-start">
                <img id="selected-book-cover" src="" alt="" class="w-40 object-cover rounded-sm shrink-0 hidden">
                <div class="flex flex-col w-full gap-3">

                    <div id="selected-book-info" class="flex items-center gap-3 p-3 bg-slate-900 border border-slate-700 rounded-lg">
                        <div class="flex-1 min-w-0">
                            <p id="selected-book-title" class="text-white text-sm font-semibold truncate"></p>
                            <p id="selected-book-author" class="text-slate-400 text-xs truncate"></p>
                        </div>
                    </div>

                    {{-- Loading Indicator --}}
                    <div id="rl-loading" class="hidden text-center py-4 text-slate-400 text-sm">
                        <svg class="w-5 h-5 animate-spin inline mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        Mengambil data...
                    </div>

                    <form id="reading-log-form" action="" method="POST" class="space-y-4">
                        @csrf

                        {{-- Status Selection --}}
                        <div>
                            <label class="text-slate-300 text-sm font-medium mb-2 block">Status Baca</label>
                            <div class="grid grid-cols-3 gap-2">
                                <label class="cursor-pointer">
                                    <input type="radio" name="status" value="want_to_read" class="hidden peer" required>
                                    <span class="block px-3 py-2.5 bg-slate-900 border border-slate-700 rounded-lg
                                            text-slate-300 text-xs text-center font-medium
                                            peer-checked:bg-purple-600 peer-checked:border-purple-500 peer-checked:text-white
                                            transition cursor-pointer">
                                        Ingin Baca
                                    </span>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="status" value="reading" class="hidden peer" required>
                                    <span class="block px-3 py-2.5 bg-slate-900 border border-slate-700 rounded-lg
                                            text-slate-300 text-xs text-center font-medium
                                            peer-checked:bg-blue-600 peer-checked:border-blue-500 peer-checked:text-white
                                            transition cursor-pointer">
                                        Sedang Baca
                                    </span>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="status" value="finished" class="hidden peer" required>
                                    <span class="block px-3 py-2.5 bg-slate-900 border border-slate-700 rounded-lg
                                            text-slate-300 text-xs text-center font-medium
                                            peer-checked:bg-green-600 peer-checked:border-green-500 peer-checked:text-white
                                            transition cursor-pointer">
                                        Selesai
                                    </span>
                                </label>
                            </div>
                        </div>

                        {{-- "Tandai Selesai Baca" shortcut button (only when existing status = reading) --}}
                        <div id="mark-finished-banner" class="hidden">
                            <button type="button" onclick="markAsFinished()"
                                class="w-full flex items-center justify-center gap-2 py-2.5 bg-green-700/30
                                       border border-green-600/50 hover:bg-green-700/50 text-green-400 text-sm
                                       font-semibold rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Tandai Selesai Baca
                            </button>
                        </div>

                        {{-- Tanggal Mulai (reading + finished) --}}
                        <div id="started-at-row" class="hidden">
                            <label class="text-slate-400 text-xs mb-1 block">Mulai Baca</label>
                            <input type="date" name="started_at" id="started-at-input"
                                class="w-full bg-slate-900 border border-slate-700 text-white text-sm rounded-lg
                                        px-3 py-2 outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500
                                        transition">
                        </div>

                        {{-- Tanggal Selesai (finished only) --}}
                        <div id="finished-at-row" class="hidden">
                            <label class="text-slate-400 text-xs mb-1 block">Selesai Baca</label>
                            <input type="date" name="finished_at" id="finished-at-input"
                                class="w-full bg-slate-900 border border-slate-700 text-white text-sm rounded-lg
                                        px-3 py-2 outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500
                                        transition">
                        </div>

                        {{-- Review Section (hanya tampil saat status = finished) --}}
                        <div id="review-section" class="hidden space-y-3 pt-2 border-t border-slate-700">
                            <p class="text-slate-300 text-sm font-medium">Review Buku <span class="text-slate-500 font-normal">(opsional)</span></p>

                            {{-- Rating --}}
                            <div>
                                <label class="text-slate-400 text-xs mb-2 block">Rating</label>
                                <div class="flex gap-1" id="star-rating-container">
                                    @for($i = 1; $i <= 5; $i++)
                                        <label class="cursor-pointer group" data-star="{{ $i }}">
                                            <input type="radio" name="rating" value="{{ $i }}" class="hidden peer">
                                            <svg class="w-7 h-7 text-slate-600 peer-checked:text-yellow-400
                                                        group-hover:text-yellow-300 transition star-icon"
                                                 fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        </label>
                                    @endfor
                                </div>
                            </div>

                            {{-- Review Text --}}
                            <div>
                                <label class="text-slate-400 text-xs mb-2 block">Ulasan</label>
                                <textarea name="review" id="review-textarea" rows="3"
                                        placeholder="Bagaimana pendapatmu tentang buku ini?"
                                        class="w-full bg-slate-900 border border-slate-700 text-white text-sm rounded-lg
                                                px-3 py-2 resize-none outline-none focus:ring-2 focus:ring-purple-500
                                                placeholder-slate-600 transition"></textarea>
                            </div>

                            {{-- Is Liked --}}
                            <label class="flex items-center gap-2.5 cursor-pointer group">
                                <input type="hidden" name="is_liked" value="0">
                                <input type="checkbox" name="is_liked" id="is-liked-checkbox" value="1"
                                       class="w-4 h-4 rounded bg-slate-900 border-slate-600
                                              accent-purple-600 transition">
                                <span class="text-slate-300 text-sm group-hover:text-white transition">
                                    Saya suka buku ini
                                </span>
                            </label>
                        </div>

                        {{-- Submit --}}
                        <button type="submit" id="rl-submit-btn"
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
let existingLogData = null;

// ── Open Helpers ────────────────────────────────────────────────

function openReadingLogModalWithBook(bookId, externalId, title, author, cover = null) {
    selectBookForReadingLog(bookId, externalId, title, author, cover);
    document.getElementById('global-reading-log-modal').classList.replace('hidden', 'flex');
}

function openReadingLogModal() {
    const modal = document.getElementById('global-reading-log-modal');
    modal.classList.replace('hidden', 'flex');
    document.getElementById('step-search').classList.remove('hidden');
    document.getElementById('step-form').classList.add('hidden');
    selectedBookId = null;
    existingLogData = null;
    document.getElementById('reading-log-search').value = '';
    document.getElementById('reading-log-results').innerHTML = '';
    document.getElementById('reading-log-results').classList.add('hidden');
    document.getElementById('reading-log-empty').classList.remove('hidden');
    document.getElementById('reading-log-search').focus();
}

function closeReadingLogModal() {
    document.getElementById('global-reading-log-modal').classList.replace('flex', 'hidden');
    document.getElementById('reading-log-search').value = '';
    document.getElementById('reading-log-results').innerHTML = '';
    document.getElementById('reading-log-results').classList.add('hidden');
    document.getElementById('reading-log-empty').classList.remove('hidden');
}

// ── UI Update Based on Status ───────────────────────────────────

function updateFormForStatus(status) {
    const startedRow      = document.getElementById('started-at-row');
    const finishedRow     = document.getElementById('finished-at-row');
    const reviewSection   = document.getElementById('review-section');
    const markFinishedBtn = document.getElementById('mark-finished-banner');

    if (status === 'want_to_read') {
        startedRow.classList.add('hidden');
        finishedRow.classList.add('hidden');
        reviewSection.classList.add('hidden');
        markFinishedBtn.classList.add('hidden');

    } else if (status === 'reading') {
        startedRow.classList.remove('hidden');
        finishedRow.classList.add('hidden');
        reviewSection.classList.add('hidden');
        // Show "Tandai Selesai" only if user already has a "reading" log
        if (existingLogData?.log?.status === 'reading') {
            markFinishedBtn.classList.remove('hidden');
        } else {
            markFinishedBtn.classList.add('hidden');
        }

    } else if (status === 'finished') {
        startedRow.classList.remove('hidden');
        finishedRow.classList.remove('hidden');
        reviewSection.classList.remove('hidden');
        markFinishedBtn.classList.add('hidden');
    }
}

function markAsFinished() {
    const finishedRadio = document.querySelector('input[name="status"][value="finished"]');
    finishedRadio.checked = true;
    updateFormForStatus('finished');
    // Keep started_at from existing log
    if (existingLogData?.log?.started_at) {
        document.getElementById('started-at-input').value = existingLogData.log.started_at;
    }
    // Set finished_at to today
    document.getElementById('finished-at-input').value = new Date().toISOString().split('T')[0];
}

// ── Pre-fill Form from Existing Log ────────────────────────────

function prefillForm(data) {
    existingLogData = data;

    // Reset form fields first
    document.getElementById('reading-log-form').reset();

    if (!data?.log) {
        // No existing log — hide all date/review sections, show clean form
        updateFormForStatus('');
        return;
    }

    const { log, review } = data;

    // Select status radio
    const statusRadio = document.querySelector(`input[name="status"][value="${log.status}"]`);
    if (statusRadio) statusRadio.checked = true;

    // Fill dates
    if (log.started_at)  document.getElementById('started-at-input').value  = log.started_at;
    if (log.finished_at) document.getElementById('finished-at-input').value = log.finished_at;

    // Fill review if exists
    if (review) {
        if (review.rating) {
            const ratingRadio = document.querySelector(`input[name="rating"][value="${review.rating}"]`);
            if (ratingRadio) ratingRadio.checked = true;
        }
        document.getElementById('review-textarea').value    = review.review  || '';
        document.getElementById('is-liked-checkbox').checked = review.is_liked || false;
    }

    updateFormForStatus(log.status);
}

// ── Search ──────────────────────────────────────────────────────

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
            const res   = await fetch(`{{ route('books.autocomplete') }}?q=${encodeURIComponent(query)}`);
            const books = await res.json();

            const resultsDiv = document.getElementById('reading-log-results');
            const emptyDiv   = document.getElementById('reading-log-empty');

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
                    ${book.cover
                        ? `<img src="${book.cover}" class="w-10 h-14 object-cover rounded shrink-0">`
                        : `<div class="w-10 h-14 bg-slate-800 rounded shrink-0 flex items-center justify-center text-slate-600 text-xs">?</div>`}
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

// ── Select Book ─────────────────────────────────────────────────

async function selectBookForReadingLog(bookId, externalId, title, author, cover = null) {
    selectedBookId = externalId;
    let finalBookId = bookId;

    if (!finalBookId || finalBookId === 'null') {
        try {
            await fetch(`{{ url('books') }}/${externalId}`);
        } catch (e) {
            console.warn('Error creating book:', e);
        }
    }

    const routeId = finalBookId && finalBookId !== 'null' ? finalBookId : externalId;

    // Update form action
    document.getElementById('reading-log-form').action =
        `{{ route('books.reading-log', ':id') }}`.replace(':id', routeId);

    // Show book info
    document.getElementById('selected-book-title').textContent  = title;
    document.getElementById('selected-book-author').textContent = author;

    const coverEl = document.getElementById('selected-book-cover');
    if (cover) {
        coverEl.src = cover;
        coverEl.classList.remove('hidden');
    } else {
        coverEl.src = '';
        coverEl.classList.add('hidden');
    }

    // Switch to form step
    document.getElementById('step-search').classList.add('hidden');
    document.getElementById('step-form').classList.remove('hidden');

    // Show loading, hide form while fetching
    document.getElementById('rl-loading').classList.remove('hidden');
    document.getElementById('reading-log-form').classList.add('hidden');

    // Fetch existing log data
    try {
        const res  = await fetch(`{{ route('books.reading-log-data', ':id') }}`.replace(':id', routeId));
        const data = await res.json();
        prefillForm(data);
    } catch (e) {
        console.warn('Could not fetch existing log:', e);
        prefillForm(null);
    } finally {
        document.getElementById('rl-loading').classList.add('hidden');
        document.getElementById('reading-log-form').classList.remove('hidden');
    }
}

// ── Back to Search ──────────────────────────────────────────────

function readingLogBackToSearch() {
    document.getElementById('step-search').classList.remove('hidden');
    document.getElementById('step-form').classList.add('hidden');
    selectedBookId  = null;
    existingLogData = null;
    document.getElementById('reading-log-search').focus();
}

// ── Status Radio Change ─────────────────────────────────────────

document.querySelectorAll('input[name="status"]').forEach(radio => {
    radio.addEventListener('change', function() {
        updateFormForStatus(this.value);
    });
});

// ── Form Submit ─────────────────────────────────────────────────

document.getElementById('reading-log-form')?.addEventListener('submit', async function(e) {
    e.preventDefault();

    const btn = document.getElementById('rl-submit-btn');
    btn.disabled  = true;
    btn.textContent = 'Menyimpan...';

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
            // Show toast or refresh
            if (window.showToast) {
                window.showToast('Reading Log berhasil disimpan!', 'success');
            } else {
                alert('Reading Log berhasil disimpan!');
            }
            // Reload to reflect changes
            window.location.reload();
        } else {
            const errorData = await res.json();
            alert(errorData.message || 'Terjadi kesalahan saat menyimpan Reading Log');
        }
    } catch (e) {
        console.error('Form submission error:', e);
        alert('Terjadi kesalahan');
    } finally {
        btn.disabled    = false;
        btn.textContent = 'Simpan Reading Log';
    }
});

// ── Close on Background Click ───────────────────────────────────

document.getElementById('global-reading-log-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeReadingLogModal();
});
</script>
