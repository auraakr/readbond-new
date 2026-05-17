<div id="diaryModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-slate-900 rounded-3xl max-w-md w-full p-6 shadow-2xl border border-white/5 transform scale-95 transition-all duration-300">
        
        {{-- Header Modal --}}
        <div class="flex items-center justify-between mb-6">
            <h2 id="modalTitle" class="text-base font-bold text-white tracking-tight">Write Entry</h2>
            <button onclick="closeDiaryModal()" class="text-slate-400 hover:text-slate-200 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="diaryForm" action="{{ route('diary.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" id="log_id" name="_method" value="POST">

            {{-- Select Book --}}
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Book Title</label>
                <select name="book_id" id="modal_book_id" class="w-full bg-slate-800 border border-white/5 text-slate-200 rounded-2xl p-3 text-xs focus:ring-1 focus:ring-purple-500 focus:border-purple-500 focus:outline-none transition-all" required>
                    <option value="" class="bg-slate-900 text-slate-400">Select a book you are reading...</option>
                    @foreach($user->readingLogs()->with('book')->get() as $log)
                        @if($log->book)
                            <option value="{{ $log->book->id }}" class="bg-slate-900 text-slate-200">{{ $log->book->title }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            {{-- Reading Progress Row --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Current Page</label>
                    <input type="number" name="current_page" id="modal_current_page" min="0" class="w-full bg-slate-800 border border-white/5 text-white rounded-2xl p-3 text-xs focus:ring-1 focus:ring-purple-500 focus:outline-none transition-all" placeholder="e.g. 120">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Pages Read Today</label>
                    <input type="number" name="pages_read" id="modal_pages_read" min="0" class="w-full bg-slate-800 border border-white/5 text-white rounded-2xl p-3 text-xs focus:ring-1 focus:ring-purple-500 focus:outline-none transition-all" placeholder="e.g. 24">
                </div>
            </div>

            {{-- Mood Selector (Aksen Ungu Sesuai Tema Baru) --}}
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">How do you feel?</label>
                <input type="hidden" name="mood" id="modal_mood_input" value="">
                <div class="flex justify-between gap-2">
                    <button type="button" onclick="setMood('happy')" class="mood-btn flex-1 py-2 rounded-2xl border border-white/5 bg-slate-800 text-base shadow-sm hover:border-purple-500 transition-all duration-200" data-mood="happy">😊</button>
                    <button type="button" onclick="setMood('sad')" class="mood-btn flex-1 py-2 rounded-2xl border border-white/5 bg-slate-800 text-base shadow-sm hover:border-purple-500 transition-all duration-200" data-mood="sad">😢</button>
                    <button type="button" onclick="setMood('excited')" class="mood-btn flex-1 py-2 rounded-2xl border border-white/5 bg-slate-800 text-base shadow-sm hover:border-purple-500 transition-all duration-200" data-mood="excited">🤩</button>
                    <button type="button" onclick="setMood('tired')" class="mood-btn flex-1 py-2 rounded-2xl border border-white/5 bg-slate-800 text-base shadow-sm hover:border-purple-500 transition-all duration-200" data-mood="tired">🥱</button>
                </div>
            </div>

            {{-- Notes Input --}}
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Journal Reflection</label>
                <textarea name="notes" id="modal_notes" rows="4" class="w-full bg-slate-800 border border-white/5 text-white rounded-2xl p-3 text-xs focus:ring-1 focus:ring-purple-500 focus:outline-none resize-none transition-all" placeholder="Write your painful or happy moments..."></textarea>
            </div>

            {{-- Reading Date --}}
            <div>
                <label class="text-slate-400 text-xs uppercase tracking-wide mb-2 block font-semibold">
                    Reading Date
                    <span class="text-slate-600 text-[10px] normal-case font-normal ml-1">
                        (Today or Yesterday only)
                    </span>
                </label>
                <input type="date" 
                    name="read_date" 
                    value="{{ date('Y-m-d') }}"
                    max="{{ date('Y-m-d') }}"
                    min="{{ date('Y-m-d', strtotime('-1 day')) }}"
                    required
                    class="w-full bg-[#14181c] border border-slate-700 text-white text-sm rounded-lg
                            px-4 py-2.5 outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500
                            transition">
            </div>

            {{-- Submit Button --}}
            <button type="submit" class="w-full py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-2xl text-xs font-bold tracking-wider uppercase transition-all shadow-lg shadow-purple-600/10 mt-2">
                Save Entry
            </button>
        </form>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('modalTitle').innerText = 'Write New Entry';
        document.getElementById('diaryForm').action = "{{ route('diary.store') }}";
        document.getElementById('log_id').value = "POST";
        document.getElementById('diaryForm').reset();
        setMood('');
        document.getElementById('diaryModal').classList.remove('hidden');
    }

    function closeDiaryModal() {
        document.getElementById('diaryModal').classList.add('hidden');
    }

    function setMood(mood) {
        document.getElementById('modal_mood_input').value = mood;
        document.querySelectorAll('.mood-btn').forEach(btn => {
            if(btn.getAttribute('data-mood') === mood) {
                // Saat aktif: Ganti border ke warna purple dan beri background transparan ungu tipis
                btn.classList.add('border-purple-500', 'bg-purple-600/10');
                btn.classList.remove('border-white/5', 'bg-slate-800');
            } else {
                // Saat tidak aktif: Kembalikan ke warna slate standar gelap
                btn.classList.remove('border-purple-500', 'bg-purple-600/10');
                btn.classList.add('border-white/5', 'bg-slate-800');
            }
        });
    }
</script>