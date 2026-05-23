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
        Schema::create('book_clubs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('moderator_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('category')->default('Just for fun');
            $table->text('rules')->nullable();
            $table->string('cover_image')->nullable();
            // Untuk fitur "Currently Reading" di dalam klub
            $table->foreignId('current_book_id')->nullable()->constrained('books')->onDelete('set null');
            $table->text('current_book_reason')->nullable();
            $table->date('current_book_finish_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_clubs');
    }
};
