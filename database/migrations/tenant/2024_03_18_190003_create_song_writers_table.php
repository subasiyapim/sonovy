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
        Schema::create('song_writers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Song::class, 'song_id')->constrained();
            $table->string('name');
            $table->json('skills')->nullable();
            $table->integer('rate')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('song_writers');
    }
};
