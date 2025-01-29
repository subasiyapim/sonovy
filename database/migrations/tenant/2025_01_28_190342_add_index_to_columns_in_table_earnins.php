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
        Schema::table('earnings', function (Blueprint $table) {
            $table->string('platform', 50)->index()->change();
            $table->string('country', 50)->index()->change();
            $table->string('label_name')->index()->change();
            $table->string('artist_name', 150)->index()->change();
            $table->string('release_name', 150)->index()->change();
            $table->string('song_name', 150)->index()->change();
            $table->string('isrc_code', 15)->index()->change();
            $table->decimal('earning', 20, 9)->index()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('earnings', function (Blueprint $table) {
            $table->dropIndex('earnings_platform_index');
            $table->dropIndex('earnings_country_index');
            $table->dropIndex('earnings_label_name_index');
            $table->dropIndex('earnings_artist_name_index');
            $table->dropIndex('earnings_release_name_index');
            $table->dropIndex('earnings_song_name_index');
            $table->dropIndex('earnings_isrc_code_index');
            $table->dropIndex('earnings_earning_index');
        });
    }
};
