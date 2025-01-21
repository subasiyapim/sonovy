<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('earnings', function (Blueprint $table) {
            // Eksik kolonları ekle
            if (!Schema::hasColumn('earnings', 'region')) {
                $table->string('region')->nullable()->after('country');
            }
            if (!Schema::hasColumn('earnings', 'streaming_type')) {
                $table->string('streaming_type')->nullable()->after('catalog_number');
            }
            if (!Schema::hasColumn('earnings', 'streaming_subscription_type')) {
                $table->string('streaming_subscription_type')->nullable()->after('streaming_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('earnings', function (Blueprint $table) {
            $table->dropColumn([
                'region',
                'streaming_type',
                'streaming_subscription_type'
            ]);
        });
    }
};
