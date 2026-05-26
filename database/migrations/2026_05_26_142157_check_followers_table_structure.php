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
        // Cek apakah tabel sudah ada
        if (!Schema::hasTable('followers')) {
            Schema::create('followers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id'); // Yang melakukan follow
                $table->unsignedBigInteger('following_id'); // Yang di-follow
                $table->timestamps();

                // Foreign keys
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('following_id')->references('id')->on('users')->onDelete('cascade');

                // Prevent duplicate follows
                $table->unique(['user_id', 'following_id']);

                // Indexes
                $table->index('user_id');
                $table->index('following_id');
            });
        } else {
            // Jika tabel sudah ada, pastikan struktur benar
            Schema::table('followers', function (Blueprint $table) {
                // Cek apakah kolom timestamps ada
                if (!Schema::hasColumn('followers', 'created_at')) {
                    $table->timestamps();
                }
            });
        }
    }

    public function down(): void
    {
        // Don't drop if checking
    }
};
