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
        Schema::create('song_musician', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Song::class, 'song_id')->constrained('songs')->onDelete('cascade');
            $table->foreignIdFor(\App\Models\User::class, 'musician_id')->constrained('users')->onDelete('cascade');
            $table->foreignIdFor(\App\Models\ArtistBranch::class,
                'branch_id')->constrained('artist_branches')->onDelete('cascade');
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
