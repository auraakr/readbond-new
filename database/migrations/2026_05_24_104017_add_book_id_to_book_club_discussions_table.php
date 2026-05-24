<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('book_club_discussions', function (Blueprint $table) {
            // Menambahkan kolom book_id yang bersifat nullable (optional)
            $table->foreignId('book_id')->nullable()->constrained('books')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('book_club_discussions', function (Blueprint $table) {
            $table->dropForeign(['book_id']);
            $table->dropColumn('book_id');
        });
    }
};
