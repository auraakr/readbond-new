@props(['book'])

<div class="rb-reviewed-item">
    <div class="rb-reviewed-item__cover">
        @if(isset($book['cover_i']))
            <img src="https://covers.openlibrary.org/b/id/{{ $book['cover_i'] }}-S.jpg" alt="{{ $book['title'] }}">
        @endif
    </div>
    <div class="rb-reviewed-item__info">
        <span class="rb-tag">Classic</span>
        <p class="rb-reviewed-item__title">{{ $book['title'] }}</p>
        <p class="rb-reviewed-item__author">{{ $book['author_name'][0] ?? 'Unknown Author' }}</p>
        <div class="rb-stars">
            <div class="rb-stars__icons">★★★★<span style="opacity:0.2">★</span></div>
            <span class="rb-stars__count">({{ number_format(rand(100, 999)) }} reviews)</span>
        </div>
    </div>
</div>
