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
        Schema::create('song_musicians', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Song::class, 'song_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->foreignIdFor(\App\Models\ArtistBranch::class, 'role_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('song_musicians');
    }
};
