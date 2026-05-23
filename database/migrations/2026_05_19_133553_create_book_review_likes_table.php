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
        Schema::create('book_review_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_review_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Mencegah user melakukan like ganda pada review yang sama
            $table->unique(['user_id', 'book_review_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_review_likes');
    }
};
