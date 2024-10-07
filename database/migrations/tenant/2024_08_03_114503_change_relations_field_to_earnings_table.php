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
        Schema::table('earnings', function (Blueprint $table) {
            $table->dropForeign(['earning_report_id']);
        });

        Schema::table('earnings', function (Blueprint $table) {
            $table->foreign('earning_report_id')->references('id')->on('earning_reports')->onDelete('cascade');
        });
    }

};
