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
        Schema::create('artist_product', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Artist::class, 'artist_id')->constrained();
            $table->foreignIdFor(\App\Models\Product::class, 'product_id')->constrained();
            $table->boolean('is_main')->default(0);
            $table->unique(['artist_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artist_product');
    }
};
