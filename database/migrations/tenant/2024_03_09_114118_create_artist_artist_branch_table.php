<?php

use App\Models\Artist;
use App\Models\ArtistBranch;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('artist_artist_branch', function (Blueprint $table) {
            $table->foreignIdFor(Artist::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(ArtistBranch::class)->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artist_artist_branch');
    }
};
