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
        Schema::create('book_club_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_club_id')->constrained('book_clubs')->onDelete('cascade');
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            
            // Status pembacaan buku bersama di dalam klub
            $table->enum('status', ['reading', 'completed', 'plan_to_read'])->default('reading');
            
            // Opsional: Mencatat siapa moderator/member yang menambahkan buku ini
            $table->foreignId('added_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();

            // Mencegah buku yang sama dimasukkan dua kali dengan status aktif yang sama
            $table->unique(['book_club_id', 'book_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_club_books');
    }
};
