<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('earning_report_files', function (Blueprint $table) {
            if (!Schema::hasColumn('earning_report_files', 'total_rows')) {
                $table->unsignedInteger('total_rows')->default(0)->after('status');
            }
            if (!Schema::hasColumn('earning_report_files', 'processed_rows')) {
                $table->unsignedInteger('processed_rows')->default(0)->after('total_rows');
            }
            if (!Schema::hasColumn('earning_report_files', 'error_rows')) {
                $table->unsignedInteger('error_rows')->default(0)->after('processed_rows');
            }
        });
    }

    public function down()
    {
        Schema::table('earning_report_files', function (Blueprint $table) {
            $table->dropColumn(['total_rows', 'processed_rows', 'error_rows']);
        });
    }
};
