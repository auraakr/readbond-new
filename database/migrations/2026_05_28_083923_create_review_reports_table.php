<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('review_reports', function (Blueprint $table) {
            $table->id();
            // Hubungkan ke user yang melaporkan
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Hubungkan ke review yang dilaporkan (sesuaikan nama tabel reviewmu jika berbeda)
            $table->foreignId('book_review_id')->constrained('book_reviews')->onDelete('cascade'); 
            
            // Alasan pelaporan (bisa pakai enum atau string biasa)
            $table->string('reason'); // Contoh: 'spam', 'spoiler', 'harassment', 'other'
            $table->text('notes')->nullable(); // Catatan tambahan opsional dari user
            
            $table->string('status')->default('pending'); // 'pending', 'reviewed', 'dismissed'
            $table->timestamps();

            // Mencegah 1 user melaporkan 1 review yang sama berkali-kali
            $table->unique(['user_id', 'book_review_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_reports');
    }
};
