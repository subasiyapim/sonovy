<?php

use App\Models\Broadcast;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('catalog_numbers', function (Blueprint $table) {
            $table->id();
            $table->string('catalog_number')->unique();
            $table->boolean('is_used')->default(false);
            $table->foreignIdFor(Broadcast::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalog_numbers');
    }
};
