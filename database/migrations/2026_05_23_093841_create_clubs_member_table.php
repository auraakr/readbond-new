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
        Schema::create('book_club_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_club_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['moderator', 'member'])->default('member');
            $table->timestamps();
            
            $table->unique(['book_club_id', 'user_id']); // Memastikan tidak bisa join 2 kali
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_club_members');
    }
};
