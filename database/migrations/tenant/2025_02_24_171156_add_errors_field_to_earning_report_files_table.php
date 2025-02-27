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
        Schema::table('earning_report_files', function (Blueprint $table) {
            $table->json('errors')->nullable()->after('processed_at');
            $table->tinyInteger('status')->default(1)->after('errors');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('earning_report_files', function (Blueprint $table) {
            $table->dropColumn('errors');
            $table->dropColumn('status');
        });
    }
};
