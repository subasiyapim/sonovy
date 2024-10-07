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
            if (!Schema::hasColumn('earning_report_files', 'name')) {
                $table->string('name')->nullable()->after('id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('earning_report_files', function (Blueprint $table) {
            //
        });
    }
};
