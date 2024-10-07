<?php

use App\Models\Country;
use App\Models\Genre;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignIdFor(Genre::class, 'genre_id')->nullable()->constrained('genres');
            $table->foreignIdFor(Genre::class, 'sub_genre_id')->nullable()->constrained('genres');

            $table->tinyInteger('type')->comment('1: Sound, 2: Video');
            $table->string('path')->nullable()->comment('/storage/songs/...');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();


            $table->string('isrc')->nullable()->unique();
            $table->boolean('is_instrumental')->default(0);
            $table->boolean('is_explicit')->default(0);
            $table->foreignIdFor(Country::class, 'language_id')->nullable()->constrained('countries');
            $table->tinyText('lyrics')->nullable();
            $table->string('iswc')->nullable()->unique();
            $table->string('preview_start')->nullable();
            $table->boolean('is_cover')->default(0);
            $table->string('remixer_artis')->nullable();
            $table->boolean('released_before')->default(0);
            $table->date('original_release_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};
