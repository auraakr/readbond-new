@props(['book'])

<div class="rb-book-card">
    <div class="rb-book-card__cover">
        @if(isset($book['cover_i']))
            <img src="https://covers.openlibrary.org/b/id/{{ $book['cover_i'] }}-M.jpg" alt="{{ $book['title'] }}">
        @else
            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#3a3a4a;font-size:11px;padding:12px;text-align:center;">No Cover</div>
        @endif
    </div>
    <p class="rb-book-card__title">{{ $book['title'] }}</p>
    <p class="rb-book-card__author">{{ $book['author_name'][0] ?? 'Unknown Author' }}</p>
</div>
