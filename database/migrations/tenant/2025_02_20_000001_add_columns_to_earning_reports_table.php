<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('earning_reports', function (Blueprint $table) {
            $table->string('period')->nullable()->after('name');
            $table->string('report_type')->nullable()->after('period');
            $table->decimal('file_size', 8, 2)->nullable()->after('report_type');
            $table->timestamp('processed_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('earning_reports', function (Blueprint $table) {
            $table->dropColumn([
                'report_type',
                'file_size',
                'processed_at',
            ]);
        });
    }
};
