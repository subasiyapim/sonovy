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
        Schema::table('products', function (Blueprint $table) {
            $table->tinyInteger('video_type')->nullable()->comment('video type 1=apple video 2=music video');
            $table->text('description')->nullable()->comment('video description');
            $table->boolean('is_for_kids')->nullable()->comment('video for kids');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('video_type');
            $table->dropColumn('description');
            $table->dropColumn('is_for_kids');
        });
    }
};
