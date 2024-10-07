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
        Schema::create('artist_broadcast', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Artist::class, 'artist_id')->constrained();
            $table->foreignIdFor(\App\Models\Broadcast::class, 'broadcast_id')->constrained();
            $table->boolean('is_main')->default(0);
            $table->unique(['artist_id', 'broadcast_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('broadcast_artist');
    }
};
