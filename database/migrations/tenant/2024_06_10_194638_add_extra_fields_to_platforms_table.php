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
        /* 'visible_name',
        'status',
        'url',
        'authenticators',
        'prices',
        */

        Schema::table('platforms', function (Blueprint $table) {
            $table->string('visible_name')->nullable()->after('name');
            $table->tinyInteger('status')->default(1)->after('code');
            $table->string('url')->nullable()->after('status');
            $table->json('authenticators')->nullable()->after('url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('platforms', function (Blueprint $table) {
            $table->dropColumn('visible_name');
            $table->dropColumn('status');
            $table->dropColumn('url');
            $table->dropColumn('authenticators');
        });
    }
};
