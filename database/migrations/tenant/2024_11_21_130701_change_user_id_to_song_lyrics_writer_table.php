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
        Schema::dropIfExists('song_lyrics_writer');

        Schema::create('song_lyrics_writer', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Song::class, 'song_id')->constrained()->cascadeOnDelete();
            $table->string('name');
        });
    }


};
