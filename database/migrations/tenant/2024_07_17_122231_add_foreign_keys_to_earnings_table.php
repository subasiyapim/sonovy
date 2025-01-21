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
            // Local foreign keys (tenant database)
            $table->foreign('platform_id')->references('id')->on('platforms')->onDelete('set null');
            $table->foreign('label_id')->references('id')->on('labels')->onDelete('set null');
            $table->foreign('artist_id')->references('id')->on('artists')->onDelete('set null');
            $table->foreign('song_id')->references('id')->on('songs')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('earning_report_id')->references('id')->on('earning_reports')->onDelete('set null');
            
            // country_id için foreign key yok çünkü countries tablosu merkezi veritabanında
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('earnings', function (Blueprint $table) {
            $table->dropForeign(['platform_id']);
            $table->dropForeign(['label_id']);
            $table->dropForeign(['artist_id']);
            $table->dropForeign(['song_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['earning_report_id']);
        });
    }
}; 