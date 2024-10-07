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
        Schema::create('convert_audio', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Broadcast::class, 'broadcast_id');
            $table->foreignIdFor(\App\Models\Song::class, 'song_id');
            $table->date('release_date');
            $table->foreignIdFor(\App\Models\Timezone::class, 'timezone_id');
            $table->time('release_time');
            $table->tinyInteger('channel_theme_id')->default(1);
            $table->string('output_file')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('convert_audio');
    }
};
