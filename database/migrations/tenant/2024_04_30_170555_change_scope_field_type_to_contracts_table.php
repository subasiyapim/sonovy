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
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('scope');
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->tinyInteger('scope')->after('auto_renewal')->comment('1: SONG, 2: ALBUM');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('scope');
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->enum('scope', ['SONG', 'ALBUM'])->after('auto_renewal');
        });
    }
};
