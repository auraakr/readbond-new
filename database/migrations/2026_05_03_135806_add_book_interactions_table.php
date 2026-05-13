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
        // Like buku
        Schema::create('book_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
 
            $table->unique(['user_id', 'book_id']);
        });
 
        // Rating buku per user (1-5)
        Schema::create('book_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating'); // 1-5
            $table->timestamps();
 
            $table->unique(['user_id', 'book_id']); // 1 user 1 rating per buku
        });
 
        // Readlist
        Schema::create('book_readlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
 
            $table->unique(['user_id', 'book_id']);
        });
 
        // Reading log
        Schema::create('reading_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['want_to_read', 'reading', 'finished'])->default('want_to_read');
            $table->date('started_at')->nullable();
            $table->date('finished_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
 
            $table->unique(['user_id', 'book_id']);
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('reading_logs');
        Schema::dropIfExists('book_readlists');
        Schema::dropIfExists('book_ratings');
        Schema::dropIfExists('book_likes');
    }
};
