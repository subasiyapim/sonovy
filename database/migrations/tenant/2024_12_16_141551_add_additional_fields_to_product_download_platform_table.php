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
            $table->tinyText('description')->nullable();
            $table->tinyText('hashtags')->nullable();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->string('content_id')->nullable();
            $table->string('privacy')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_download_platform', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('hashtags');
            $table->dropColumn('date');
            $table->dropColumn('time');
            $table->dropColumn('content_id');
            $table->dropColumn('privacy');
        });
    }
};
