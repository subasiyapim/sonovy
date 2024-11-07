<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->string('version')->nullable();
            $table->dropColumn('is_cover');
            $table->dropColumn('remixer_artis');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->dropColumn('version');
            $table->boolean('is_cover')->nullable();
            $table->unsignedBigInteger('remixer_artis')->nullable();
        });
    }
};
