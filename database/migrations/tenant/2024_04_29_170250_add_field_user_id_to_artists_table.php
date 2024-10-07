<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('artists', function (Blueprint $table) {
            $table->foreignId('added_by')->nullable()->after('website')->constrained('users');
        });

        DB::table('artists')->whereNull('added_by')->update(['added_by' => 1]);

        Schema::table('artists', function (Blueprint $table) {
            $table->foreignId('added_by')->nullable(false)->change();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artists', function (Blueprint $table) {
            $table->dropForeign(['added_by']);
            $table->dropColumn('added_by');
        });
    }
};
