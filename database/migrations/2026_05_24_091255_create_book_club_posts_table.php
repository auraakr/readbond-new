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
        Schema::create('book_club_posts', function (Blueprint $table) {
            $table->id();
            // Menghubungkan post ke topik diskusi tertentu
            $table->foreignId('discussion_id')
                  ->constrained('book_club_discussions')
                  ->onDelete('cascade');
            
            // Menghubungkan post ke user yang menulis pesan tersebut
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            
            // Isi dari postingan/komentar diskusi
            $table->text('content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_club_posts');
    }
};
