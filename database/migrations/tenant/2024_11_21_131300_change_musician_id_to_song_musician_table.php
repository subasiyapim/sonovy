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
        Schema::dropIfExists('song_musician');

        Schema::create('song_musician', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Song::class, 'song_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->foreignIdFor(\App\Models\ArtistBranch::class, 'role_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('song_musician');
    }
};
