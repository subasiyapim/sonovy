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
        Schema::table('earning_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('earning_reports', 'status')) {
                $table->string('status')->nullable()->after('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('earning_reports', function (Blueprint $table) {
            //
        });
    }
};
