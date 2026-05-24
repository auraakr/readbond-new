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
        Schema::table('book_clubs', function (Blueprint $table) {
            // Menambahkan field baru setelah kolom cover_image yang sudah ada sebelumnya
            $table->enum('visibility', ['public', 'private'])->default('public')->after('cover_image');
            $table->boolean('allow_member_add_book')->default(true)->after('visibility');
            $table->boolean('allow_member_add_discussion')->default(false)->after('allow_member_add_book');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_clubs', function (Blueprint $table) {
            // Drop kolom jika dilakukan rollback migration
            $table->dropColumn(['visibility', 'allow_member_add_book', 'allow_member_add_discussion']);
        });
    }
};
