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
        Schema::create('broadcast_song', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Broadcast::class, 'broadcast_id')->constrained('broadcasts');
            $table->foreignIdFor(\App\Models\Song::class, 'song_id')->constrained('songs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('broadcast_song');
    }
};
