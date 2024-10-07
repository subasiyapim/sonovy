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
        Schema::table('labels', function (Blueprint $table) {
            if (!Schema::hasColumn('labels', 'phone')) {
                $table->string('phone')->after('country_id')->nullable();
            }
            if (!Schema::hasColumn('labels', 'web')) {
                $table->string('web')->after('phone')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('labels', function (Blueprint $table) {
            $table->dropColumn('phone');
            $table->dropColumn('web');
        });
    }
};
