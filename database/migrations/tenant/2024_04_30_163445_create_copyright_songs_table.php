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
        Schema::create('copyright_songs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('copyright_id')->constrained();
            $table->foreignId('song_id')->constrained();
            $table->string('url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('copyright_songs');
    }
};
