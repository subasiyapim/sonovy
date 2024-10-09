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
        Schema::table('product_download_platform', function (Blueprint $table) {
            $table->renameColumn('platform_release_date', 'publish_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_download_platform', function (Blueprint $table) {
            $table->renameColumn('publish_date', 'platform_release_date');
        });
    }
};
