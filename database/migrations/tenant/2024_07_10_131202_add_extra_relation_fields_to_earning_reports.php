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
        Schema::table('earning_reports', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Platform::class, 'platform_id')->nullable()->after('platform');
            $table->foreignIdFor(\App\Models\Country::class, 'country_id')->nullable()->after('country');
            $table->foreignIdFor(\App\Models\Label::class, 'label_id')->nullable()->after('label_name');
            $table->foreignIdFor(\App\Models\Artist::class, 'artist_id')->nullable()->after('artist_name');
            $table->foreignIdFor(\App\Models\Song::class, 'song_id')->nullable()->after('song_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('earning_reports', function (Blueprint $table) {
            $table->dropForeign(['platform_id']);
            $table->dropForeign(['country_id']);
            $table->dropForeign(['label_id']);
            $table->dropForeign(['artist_id']);
            $table->dropForeign(['song_id']);
            $table->dropColumn(['platform_id', 'country_id', 'label_id', 'artist_id', 'song_id']);
        });
    }
};
