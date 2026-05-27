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
        Schema::create('book_user_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Gunakan 'book_id' jika kamu menyimpan detail buku di DB lokal, 
            // atau gunakan 'string/integer' jika kamu memakai external_id API.
            $table->foreignId('book_id')->constrained()->onDelete('cascade'); 
            
            // Kolom opsional untuk menentukan urutan poster (1, 2, 3, 4) saat di-drag
            $table->integer('order_position')->default(0); 
            $table->timestamps();

            // Mencegah user memfavoritkan buku yang sama dua kali di etalase
            $table->unique(['user_id', 'book_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_user_favorites');
    }
};
