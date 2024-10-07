<?php

use App\Models\User;
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
            $table->foreignIdFor(\App\Models\EarningReportFile::class, 'earning_report_file_id')->nullable()->change();
            $table->foreignIdFor(User::class, 'user_id')->nullable()->after('earning_report_file_id')->constrained();
            $table->string('name')->after('user_id')->nullable();
            $table->date('sales_date')->nullable()->change();
            $table->string('platform')->nullable()->change();
            $table->string('country')->nullable()->change();
            $table->string('label_name')->nullable()->change();
            $table->string('artist_name')->nullable()->change();
            $table->string('release_name')->nullable()->change();
            $table->string('song_name')->nullable()->change();
            $table->string('upc_code')->nullable()->change();
            $table->string('sales_type')->nullable()->change();
            $table->string('quantity')->nullable()->change();
            $table->string('currency')->nullable()->change();

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
