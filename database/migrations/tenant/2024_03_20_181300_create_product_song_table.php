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
        Schema::create('product_song', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Product::class, 'product_id')->constrained('products');
            $table->foreignIdFor(\App\Models\Song::class, 'song_id')->constrained('songs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_song');
    }
};
