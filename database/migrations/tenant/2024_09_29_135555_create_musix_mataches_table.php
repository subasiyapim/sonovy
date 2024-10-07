<?php

use App\Models\Song;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('musix_mataches', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Song::class)->constrained()->cascadeOnDelete();
            $table->string('track_id');
            $table->string('track_name');
            $table->string('commontrack_id');
            $table->boolean('instrumental');
            $table->boolean('has_lyrics');
            $table->boolean('has_subtitles');
            $table->string('album_id');
            $table->string('album_name');
            $table->string('artist_id');
            $table->string('artist_name');
            $table->string('track_share_url')->nullable();
            $table->string('lyrics_id')->nullable();
            $table->boolean('explicit')->nullable();
            $table->text('lyrics_body')->nullable();
            $table->string('lyrics_language')->nullable();
            $table->text('script_tracking_url')->nullable();
            $table->text('pixel_tracking_url')->nullable();
            $table->string('lyrics_copyright')->nullable();
            $table->timestamp('updated_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('musix_mataches');
    }
};
