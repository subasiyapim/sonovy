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
        Schema::create('title_translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale')->index();
            $table->string('title');

            $table->unique(['title_id', 'locale']);
            $table->foreignId('title_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('title_translations');
    }
};
