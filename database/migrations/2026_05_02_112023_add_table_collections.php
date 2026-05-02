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
        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // kurator/pembuat
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('cover')->nullable();       // cover custom jika ada
            $table->boolean('is_featured')->default(false); // untuk Featured Collection
            $table->unsignedInteger('likes_count')->default(0);
            $table->timestamps();
        });
 
        // Pivot: buku-buku di dalam koleksi
        Schema::create('collection_book', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('order')->default(0); // urutan buku di koleksi
            $table->timestamps();
 
            $table->unique(['collection_id', 'book_id']); // 1 buku tidak duplikat
        });
 
        // Komentar pada koleksi
        Schema::create('collection_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->unsignedTinyInteger('rating')->nullable(); // 1-5, opsional
            $table->unsignedInteger('likes_count')->default(0);
            $table->timestamps();
        });
 
        // Likes pada koleksi (agar tidak double like)
        Schema::create('collection_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
 
            $table->unique(['collection_id', 'user_id']);
        });
 
        // Likes pada komentar
        Schema::create('collection_comment_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_comment_id')
                  ->constrained('collection_comments')
                  ->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
 
            $table->unique(['collection_comment_id', 'user_id']);
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('collection_comment_likes');
        Schema::dropIfExists('collection_likes');
        Schema::dropIfExists('collection_comments');
        Schema::dropIfExists('collection_book');
        Schema::dropIfExists('collections');
    }
};
