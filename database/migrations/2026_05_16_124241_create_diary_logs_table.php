<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('diary_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->date('read_date'); // Tanggal baca buku
            $table->text('notes')->nullable(); // Catatan diary
            $table->integer('pages_read')->nullable(); // Halaman yang dibaca hari ini
            $table->integer('current_page')->nullable(); // Halaman saat ini
            $table->string('mood')->nullable(); // Mood saat membaca
            $table->boolean('is_favorite')->default(false); // Tandai sebagai favorit
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('book_id');
            $table->index('read_date');
            $table->unique(['user_id', 'book_id', 'read_date']); // Satu entry per hari per buku
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diary_logs');
    }
};
