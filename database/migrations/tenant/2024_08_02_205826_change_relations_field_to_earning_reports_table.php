<?php

use App\Models\EarningReportFile;
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
            $table->dropForeign(['earning_report_file_id']);
        });

        Schema::table('earning_reports', function (Blueprint $table) {
            $table->foreign('earning_report_file_id')->references('id')->on('earning_report_files')->cascadeOnDelete();
        });
    }
};
