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
        Schema::table('books', function (Blueprint $table) {
            // 1. Tambahkan external_id sebagai jembatan ke OpenLibrary API
            $table->string('external_id')->unique()->nullable()->after('id');

            // 2. Ubah kolom yang ada menjadi nullable agar tidak error jika API tidak mengirim data
            $table->text('desc')->nullable()->change();
            $table->integer('year')->nullable()->change();
            $table->integer('pageCount')->nullable()->default(0)->change();
            
            // 3. Buat author_id nullable jika kamu ingin menyimpan buku tanpa harus buat author dulu
            $table->foreignId('author_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('external_id');
        });
    }
};
